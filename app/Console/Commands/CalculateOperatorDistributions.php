<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use PDOException;

class CalculateOperatorDistributions extends Command
{
    protected $signature = 'distributions:operator 
                            {--date= : Distribution date (YYYY-MM-DD), defaults to yesterday}
                            {--machine= : Specific machine ID to calculate}';
    
    protected $description = 'Calculate daily operator distributions for all active machines';

    public function handle()
    {
        $date = $this->option('date') ?: Carbon::yesterday()->format('Y-m-d');
        $machineId = $this->option('machine');
        
        $this->info("Starting operator distribution calculation for {$date}");
    
        
        if ($machineId) {
            $this->calculateSpecificMachineDistribution($machineId, $date);
        } else {
            $this->calculateAllMachinesDistribution($date);
        }
        
        $this->info('Operator distribution calculation completed!');
        return 0;
    }
    
    private function calculateAllMachinesDistribution($date)
    {
        $this->info('Calculating operator distributions for all machines...');
        
        try {
            // Call stored procedure for all machines
            $result = DB::select('CALL CalculateAllOperatorDistributions(?)', [$date]);
            
            if (!empty($result) && is_object($result[0])) {
                $summary = $result[0];
                
                // Safely access properties with fallback values
                $machinesProcessed = property_exists($summary, 'machines_processed') ? $summary->machines_processed : 0;
                $operatorsPaid = property_exists($summary, 'operators_paid') ? $summary->operators_paid : 0;
                $totalDistributed = property_exists($summary, 'total_distributed') ? $summary->total_distributed : 0;
                
                $this->info("✓ Operator distributions completed successfully");
                $this->line(" Machines processed: {$machinesProcessed}");
                $this->line(" Operators paid: {$operatorsPaid}");
                $this->line(" Total distributed: $" . number_format($totalDistributed, 2));
                
                // Send summary email
                $this->sendOperatorDistributionSummary($date, $machinesProcessed, $operatorsPaid, $totalDistributed);
                
                // Show detailed breakdown if requested
                if ($this->option('verbose')) {
                    $this->showDetailedBreakdown($date);
                }
                
            } else {
                $this->warn("⚠ No operator distribution data returned for {$date}");
            }
            
        } catch (PDOException $e) {
            $this->error("❌ Database error: " . $e->getMessage());
            Log::error("Database error in operator distribution: " . $e->getMessage());
            $this->sendErrorAlert('Operator Distribution - Database Error', $e->getMessage());
            return 1;
            
        } catch (Exception $e) {
            $this->error("❌ Critical error: " . $e->getMessage());
            Log::error("Critical error in operator distribution: " . $e->getMessage());
            $this->sendErrorAlert('Operator Distribution', $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Calculate operator distributions for a specific machine
     */
    private function calculateSpecificMachineDistribution($machineId, $date)
    {
        $this->info("Calculating operator distributions for machine {$machineId}...");
        
        try {
            // Get machine details first
            $machine = DB::select("
                SELECT machine_id, location, profit_share_operators 
                FROM machines 
                WHERE machine_id = ? AND status = 'Active'
            ", [$machineId]);
            
            if (empty($machine)) {
                $this->error("❌ Machine {$machineId} not found or not active");
                return 1;
            }
            
            $machineData = $machine[0];
            $this->info("Processing machine: {$machineData->location}");
            
            // Call stored procedure for specific machine
            $result = DB::select('CALL CalculateOperatorDailyDistribution(?, ?)', [$machineId, $date]);
            
            if (!empty($result) && is_object($result[0])) {
                $summary = $result[0];
                
                $dailyRevenue = property_exists($summary, 'total_daily_revenue') ? $summary->total_daily_revenue : 0;
                $operatorRevenue = property_exists($summary, 'machine_operator_revenue') ? $summary->machine_operator_revenue : 0;
                $operatorPercentage = property_exists($summary, 'total_operator_percentage') ? $summary->total_operator_percentage : 0;
                $status = property_exists($summary, 'status') ? $summary->status : 'UNKNOWN';
                
                if ($status === 'SUCCESS') {
                    $this->info("✓ Machine {$machineId} distribution completed");
                    $this->line("   📊 Daily revenue: $" . number_format($dailyRevenue, 2));
                    $this->line("   👥 Operator share: $" . number_format($operatorRevenue, 2) . " ({$operatorPercentage}%)");
                    
                    // Show individual operator distributions
                    $this->showMachineOperatorBreakdown($machineId, $date);
                    
                } else {
                    $this->warn("⚠ Machine {$machineId} - No revenue to distribute");
                }
                
                Log::info("Operator distribution for machine {$machineId} completed with status: {$status}");
                
            } else {
                $this->warn("⚠ No distribution data returned for machine {$machineId}");
            }
            
        } catch (Exception $e) {
            $this->error("❌ Failed to calculate distributions for machine {$machineId}: " . $e->getMessage());
            Log::error("Failed to calculate operator distribution for machine {$machineId}: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Show detailed breakdown of operator distributions
     */
    private function showDetailedBreakdown($date)
    {
        $this->info("\n📋 Detailed Operator Distribution Breakdown:");
        
        try {
            $distributions = DB::select("
                SELECT 
                    od.machine_id,
                    m.location,
                    u.full_name as operator_name,
                    od.operator_role,
                    od.operator_percentage,
                    od.distribution_amount,
                    od.machine_daily_revenue
                FROM operator_distributions od
                INNER JOIN machines m ON od.machine_id = m.machine_id
                INNER JOIN users u ON od.user_id = u.user_id
                WHERE DATE(od.distribution_date) = ?
                ORDER BY od.machine_id, od.distribution_amount DESC
            ", [$date]);
            
            if (empty($distributions)) {
                $this->warn("No detailed distribution data found");
                return;
            }
            
            $currentMachine = null;
            foreach ($distributions as $dist) {
                if ($currentMachine !== $dist->machine_id) {
                    $currentMachine = $dist->machine_id;
                    $this->line("\n🏭 Machine {$dist->machine_id} ({$dist->location}) - Revenue: $" . number_format($dist->machine_daily_revenue, 2));
                }
                
                $this->line("   👤 {$dist->operator_name} ({$dist->operator_role}): {$dist->operator_percentage}% = $" . number_format($dist->distribution_amount, 2));
            }
            
        } catch (Exception $e) {
            $this->error("Failed to show detailed breakdown: " . $e->getMessage());
        }
    }
    
    /**
     * Show operator breakdown for specific machine
     */
    private function showMachineOperatorBreakdown($machineId, $date)
    {
        try {
            $operators = DB::select("
                SELECT 
                    u.full_name as operator_name,
                    od.operator_role,
                    od.operator_percentage,
                    od.distribution_amount
                FROM operator_distributions od
                INNER JOIN users u ON od.user_id = u.user_id
                WHERE od.machine_id = ? 
                AND DATE(od.distribution_date) = ?
                ORDER BY od.distribution_amount DESC
            ", [$machineId, $date]);
            
            if (!empty($operators)) {
                $this->line("\n   👥 Operator Breakdown:");
                foreach ($operators as $operator) {
                    $this->line("      • {$operator->operator_name} ({$operator->operator_role}): {$operator->operator_percentage}% = $" . number_format($operator->distribution_amount, 2));
                }
            }
            
        } catch (Exception $e) {
            $this->warn("Could not retrieve operator breakdown: " . $e->getMessage());
        }
    }
    
    /**
     * Get operator summary statistics
     */
    private function getOperatorSummaryStats($date)
    {
        try {
            $stats = DB::select("
                SELECT 
                    COUNT(DISTINCT od.machine_id) as machines_with_operators,
                    COUNT(DISTINCT od.user_id) as total_operators,
                    COUNT(*) as total_distributions,
                    SUM(od.distribution_amount) as total_amount,
                    AVG(od.distribution_amount) as avg_distribution,
                    MAX(od.distribution_amount) as max_distribution,
                    MIN(od.distribution_amount) as min_distribution
                FROM operator_distributions od
                WHERE DATE(od.distribution_date) = ?
            ", [$date]);
            
            return $stats[0] ?? null;
            
        } catch (Exception $e) {
            Log::error("Failed to get operator summary stats: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Send operator distribution summary email
     */
    private function sendOperatorDistributionSummary($date, $machinesProcessed, $operatorsPaid, $totalDistributed)
    {
        try {
            $stats = $this->getOperatorSummaryStats($date);
            
            $this->call('notifications:summary', [
                'type' => 'Operator Distribution',
                'date' => $date,
                'success' => $machinesProcessed,
                'errors' => 0,
                'details' => "Operators paid: {$operatorsPaid}, Total distributed: $" . number_format($totalDistributed, 2) . 
                           ($stats ? ", Avg per operator: $" . number_format($stats->avg_distribution, 2) : "")
            ]);
            
        } catch (Exception $e) {
            Log::error("Failed to send operator distribution summary: " . $e->getMessage());
        }
    }
    
    /**
     * Send error alert email
     */
    private function sendErrorAlert($process, $error)
    {
        $this->call('notifications:error-alert', [
            'process' => $process,
            'error' => $error
        ]);
    }
    
    /**
     * Validate machine operators configuration
     */
    private function validateMachineOperators($machineId)
    {
        try {
            $result = DB::select("
                SELECT 
                    m.machine_id,
                    m.profit_share_operators,
                    COALESCE(SUM(opa.percentage), 0) as total_assigned_percentage,
                    COUNT(opa.assignment_id) as operator_count
                FROM machines m
                LEFT JOIN operationalpartnerassignments opa ON m.machine_id = opa.machine_id
                    AND opa.role IN ('Space Owner', 'Money Collector', 'Maintenance', 'Admin')
                WHERE m.machine_id = ?
                GROUP BY m.machine_id, m.profit_share_operators
            ", [$machineId]);
            
            if (!empty($result)) {
                $data = $result[0];
                
                if ($data->total_assigned_percentage != $data->profit_share_operators) {
                    $this->warn("⚠ Machine {$machineId}: Operator percentages ({$data->total_assigned_percentage}%) don't match machine operator share ({$data->profit_share_operators}%)");
                    return false;
                }
                
                if ($data->operator_count == 0) {
                    $this->warn("⚠ Machine {$machineId}: No operators assigned");
                    return false;
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->warn("Could not validate machine operators: " . $e->getMessage());
            return false;
        }
    }
}
