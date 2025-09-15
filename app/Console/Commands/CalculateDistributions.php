<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use PDOException;

class CalculateDistributions extends Command
{
    protected $signature = 'distributions:calculate 
                            {--date= : Distribution date (YYYY-MM-DD), defaults to yesterday}
                            {--pool= : Specific pool ID to calculate}
                            {--type=both : Type of distribution (investor|operator|both)}';

    protected $description = 'Calculate daily distributions for investors and/or operators';

    public function handle()
    {
        $date = $this->option('date') ?: Carbon::yesterday()->format('Y-m-d');
        $poolId = $this->option('pool');
        $type = $this->option('type');

        $this->info("Calculating distributions for {$date}");

        if ($type === 'investor' || $type === 'both') {
            $this->calculateInvestorDistributions($date, $poolId);
        }

        if ($type === 'operator' || $type === 'both') {
            $this->calculateOperatorDistributions($date);
        }

        $this->info('Distribution calculation completed!');
        return 0;
    }

    /**
     * Calculate investor distributions for all active pools
     */
    private function calculateInvestorDistributions($date, $poolId = null)
    {
        $this->info('Calculating investor distributions...');
        Log::info("Starting investor distribution calculation for {$date}");

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            // Get active pools
            if ($poolId) {
                $pools = DB::select("SELECT pool_id, pool_name FROM investment_pools WHERE pool_id = ? AND status = 'Active'", [$poolId]);
            } else {
                $pools = DB::select("
                    SELECT DISTINCT ip.pool_id, ip.pool_name 
                    FROM investment_pools ip
                    INNER JOIN pool_investments pi ON ip.pool_id = pi.pool_id
                    WHERE ip.status = 'Active' AND pi.status = 'Active'
                ");
            }

            if (empty($pools)) {
                $this->warn('No active pools found');
                return;
            }

            $this->info("Processing " . count($pools) . " pools");
            $bar = $this->output->createProgressBar(count($pools));

            foreach ($pools as $pool) {
                try {
                    $result = DB::select('CALL CalculatePoolDailyDistribution(?, ?)', [
                        $pool->pool_id,
                        $date
                    ]);

                    if (!empty($result) && is_object($result[0])) {
                        $resultData = $result[0];

                        if (property_exists($resultData, 'status') && $resultData->status === 'SUCCESS') {
                            $successCount++;
                            $revenue = property_exists($resultData, 'total_investor_revenue') ? $resultData->total_investor_revenue : 0;
                            $this->line(" ✓ Pool {$pool->pool_id} ({$pool->pool_name}) - Revenue: $" . number_format($revenue, 2));
                            Log::info("Investor distribution completed for pool {$pool->pool_id}");
                        } else {
                            $this->line(" ⚠ Pool {$pool->pool_id} - No revenue");
                        }
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    $errors[] = "Pool {$pool->pool_id}: " . $e->getMessage();
                    $this->error(" ✗ Pool {$pool->pool_id} failed: " . $e->getMessage());
                    Log::error("Investor distribution failed for pool {$pool->pool_id}: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            // Send summary email
            $this->sendDistributionSummary('Investor', $date, $successCount, $errorCount, $errors);
        } catch (Exception $e) {
            $this->error('Critical error: ' . $e->getMessage());
            Log::error('Critical error in investor distribution: ' . $e->getMessage());
        }

        $this->info("Completed! Success: {$successCount}, Errors: {$errorCount}");
    }

    /**
     * Calculate operator distributions
     */
    private function calculateOperatorDistributions($date)
    {
        $this->info('Calculating operator distributions...');
        Log::info("Starting operator distribution calculation for {$date}");

        try {
            $result = DB::select('CALL CalculateAllOperatorDistributions(?)', [$date]);

            if (!empty($result) && is_object($result[0])) {
                $summary = $result[0];

                $machinesProcessed = property_exists($summary, 'machines_processed') ? $summary->machines_processed : 0;
                $operatorsPaid = property_exists($summary, 'operators_paid') ? $summary->operators_paid : 0;
                $totalDistributed = property_exists($summary, 'total_distributed') ? $summary->total_distributed : 0;

                $this->info(" ✓ Operator distributions completed");
                $this->info("   Machines: {$machinesProcessed}, Operators: {$operatorsPaid}, Total: $" . number_format($totalDistributed, 2));

                Log::info("Operator distribution completed: {$machinesProcessed} machines, {$operatorsPaid} operators");

                // Send summary email
                $this->sendDistributionSummary('Operator', $date, $machinesProcessed, 0, []);
            }
        } catch (Exception $e) {
            $this->error('Critical error: ' . $e->getMessage());
            Log::error('Critical error in operator distribution: ' . $e->getMessage());
        }
    }

    /**
     * Send distribution summary email
     */
    private function sendDistributionSummary($type, $date, $successCount, $errorCount, $errors)
    {
        $this->call('notifications:summary', [
            'type' => $type,
            'date' => $date,
            'success' => $successCount,
            'errors' => $errorCount,
            'details' => implode("\n", $errors)
        ]);
    }
}
