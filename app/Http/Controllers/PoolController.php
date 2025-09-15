<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoolController extends Controller
{
    
    public function viewAllPool(Request $request)
    {
        // Start with the base query
        $query = DB::table('investment_pools')
            ->leftJoin('poolfunds', 'investment_pools.pool_id', '=', 'poolfunds.pool_id')
            ->leftJoin('users', 'investment_pools.manager_user_id', '=', 'users.user_id');

        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('investment_pools.pool_id', 'like', "%{$search}%")
                    ->orWhere('investment_pools.pool_name', 'like', "%{$search}%")
                    ->orWhere('investment_pools.status', 'like', "%{$search}%");
            });
        }

        // Get paginated results with selected columns
        $pools = $query->select(
            'investment_pools.*',
            'users.full_name as manager_name',
            'users.user_type',  // Add this line to select user_type
            'poolfunds.total_pool_fund',
            'poolfunds.available_fund',
            'poolfunds.operating_fund'
        )
            ->orderBy('investment_pools.pool_id', 'desc')
            ->paginate(10);

        // Retrieve managers for the dropdown list
        $managers = DB::table('users')
            ->whereIn('user_type', [2, 3, 4, 5]) // 2→Space Owner, 3→Money Collector, 4→Maintenance, 5→Admin
            ->select('user_id', 'full_name as name', 'user_type')
            ->orderBy('full_name')
            ->get();

        return view('pages.pool.view_all_pool', compact('pools', 'managers'));
    }
    public function createPool(Request $request)
    {
        // Validate the request data
        $request->validate([
            'pool_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_user_id' => 'required|integer|exists:users,user_id',
            'target_fund' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive,Closed',
            'terms_and_conditions' => 'nullable|string',
            'profit_sharing_model' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3'
        ]);

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Insert into investment_pools table
            $poolId = DB::table('investment_pools')->insertGetId([
                'pool_name' => $request->pool_name,
                'description' => $request->description,
                'manager_user_id' => $request->manager_user_id,
                'target_fund' => $request->target_fund,
                'status' => $request->status,
                'terms_and_conditions' => $request->terms_and_conditions,
                'profit_sharing_model' => $request->profit_sharing_model,
                'currency' => $request->currency,
                'creation_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Initialize poolfunds record
            DB::table('poolfunds')->insert([
                'pool_id' => $poolId,
                'total_pool_fund' => 0.00,
                'available_fund' => 0.00,
                'operating_fund' => 0.00,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Commit the transaction
            DB::commit();

            return redirect()->route('pools.all')->with('success', 'Investment pool created successfully!');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create investment pool: ' . $e->getMessage());
        }
    }

    public function viewPoolDetail($id)
    {
        // Fetch pool details
        $pool = DB::table('investment_pools')
            ->where('pool_id', $id)
            ->first();

        if (!$pool) {
            return redirect()->route('pools.all')->with('error', 'Pool not found.');
        }

        // Fetch pool fund details
        $poolfund = DB::table('poolfunds')
            ->where('pool_id', $id)
            ->first();

        if (!$poolfund) {
            // Create a default poolfund object if none exists
            $poolfund = (object)[
                'total_pool_fund' => 0.00,
                'available_fund' => 0.00,
                'operating_fund' => 0.00,
                'last_updated' => now()
            ];
        }

        // Get manager details
        $manager = DB::table('users')
            ->where('user_id', $pool->manager_user_id)
            ->first();

        // Get all managers for the dropdown in edit form
        $managers = DB::table('users')
            ->whereIn('user_type', [2, 3, 4, 5]) // 2→Space Owner, 3→Money Collector, 4→Maintenance, 5→Admin
            ->select('user_id', 'full_name')
            ->orderBy('full_name')
            ->get();

        // Get pool investments - Fixed column name from investor_user_id to user_id
        $poolInvestments = DB::table('pool_investments')
            ->join('users', 'pool_investments.user_id', '=', 'users.user_id')
            ->where('pool_investments.pool_id', $id)
            ->select(
                'pool_investments.*',
                'users.full_name as investor_name'
            )
            ->orderBy('investment_date', 'desc')
            ->get();

        // Aggregate data for machines belonging to this specific pool
        $machineStats = DB::table('machines')
        ->where('pool_id', $id) // Filter by current pool ID
        ->selectRaw('
            COUNT(*) as total_machine_asset,
            SUM(total_sales_volume) as total_water_sale,
            SUM(bottles_saved_count) as total_bottle_sale,
            SUM(total_revenue) as total_revenue
        ')
        ->first();

        // Handle case where no machines exist for this pool
        if (!$machineStats || $machineStats->total_machine_asset == 0) {
            $machineStats = (object)[
                'total_machine_asset' => 0.00,
                'total_water_sale' => 0.00,
                'total_bottle_sale' => 0,
                'total_machines_count' => 0
            ];
        }

        // Add sales data for the graph (from root method)
        $groupType = request()->get('group_type', 'monthly'); // Default to monthly if not specified

        // Adjust SQL based on group type
        switch ($groupType) {
            case 'weekly':
                $groupBy = DB::raw("YEARWEEK(sale_date_time) as group_label");
                break;
            case 'monthly':
                $groupBy = DB::raw("DATE_FORMAT(sale_date_time, '%Y-%m') as group_label");
                break;
            case 'yearly':
                $groupBy = DB::raw("YEAR(sale_date_time) as group_label");
                break;
            default:
                $groupBy = DB::raw("DATE_FORMAT(sale_date_time, '%Y-%m') as group_label");
                $groupType = 'monthly';
                break;
        }

        // Query for average machine sale data
        $salesQuery = DB::table('machinerevenuetransactions')
            ->select($groupBy, DB::raw("AVG(sale_amount) as average_sale"))
            ->groupBy('group_label')
            ->orderBy('group_label', 'asc')
            ->get();

        // Format data for the graph
        $salesData = $salesQuery->map(function ($sale) {
            return [
                'label' => $sale->group_label,
                'average' => round($sale->average_sale, 2),
            ];
        });

        return view('pages.pool.view_pool_detail', compact('pool', 'poolfund', 'manager', 'managers', 'poolInvestments', 'machineStats', 'groupType', 'salesData'));
    }


    public function updatePool(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'pool_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_user_id' => 'required|integer|exists:users,user_id',
            'target_fund' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive,Closed',
            'terms_and_conditions' => 'nullable|string',
            'profit_sharing_model' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3'
        ]);

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Update investment_pools table
            DB::table('investment_pools')
                ->where('pool_id', $id)
                ->update([
                    'pool_name' => $request->pool_name,
                    'description' => $request->description,
                    'manager_user_id' => $request->manager_user_id,
                    'target_fund' => $request->target_fund,
                    'status' => $request->status,
                    'terms_and_conditions' => $request->terms_and_conditions,
                    'profit_sharing_model' => $request->profit_sharing_model,
                    'currency' => $request->currency,
                    'updated_at' => now()
                ]);

            // Commit the transaction
            DB::commit();

            return redirect()->route('pools.all')->with('success', 'Investment pool updated successfully!');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update investment pool: ' . $e->getMessage());
        }
    }

    public function deletePool($id)
    {
        // Validate the pool ID
        $pool = DB::table('investment_pools')->where('pool_id', $id)->first();
        if (!$pool) {
            return redirect()->route('pools.all')->with('error', 'Pool not found.');
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Delete from investment_pools table
            DB::table('investment_pools')->where('pool_id', $id)->delete();

            // Delete from poolfunds table
            DB::table('poolfunds')->where('pool_id', $id)->delete();

            // Commit the transaction
            DB::commit();

            return redirect()->route('admin.view_all_pool')->with('success', 'Investment pool deleted successfully!');
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to delete investment pool: ' . $e->getMessage());
        }
    }

    public function investPool(Request $request)
    {
        // Validate the request data
        $request->validate([
            'pool_id' => 'required|integer|exists:investment_pools,pool_id',
            'investment_amount' => 'required|numeric|min:1',
        ]);

        // Check if the pool exists and is active
        $pool = DB::table('investment_pools')
            ->where('pool_id', $request->pool_id)
            ->where('status', 'Active')
            ->first();

        if (!$pool) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Investment pool is not available for investments.');
        }

        // Get user details
        $user = auth()->user();

        // Verify the user is an investor (user_type = 1)
        if ($user->user_type != 1) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Only investors can make investments.');
        }

        // Check if user has already invested in this pool (optional - remove if multiple investments allowed)
        $existingInvestment = DB::table('pool_investments')
            ->where('pool_id', $request->pool_id)
            ->where('user_id', $user->user_id)
            ->where('status', 'Active')
            ->first();

        if ($existingInvestment) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You already have an active investment in this pool.');
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Insert investment record
            $investmentId = DB::table('pool_investments')->insertGetId([
                'pool_id' => $request->pool_id,
                'user_id' => $user->user_id,
                'investment_amount' => $request->investment_amount,
                'investment_date' => now(),
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update pool funds
            $poolfund = DB::table('poolfunds')
                ->where('pool_id', $request->pool_id)
                ->first();

            if ($poolfund) {
                // Update existing pool funds
                $totalPoolFund = $poolfund->total_pool_fund + $request->investment_amount;
                $availableFund = $poolfund->available_fund + $request->investment_amount;

                DB::table('poolfunds')
                    ->where('pool_id', $request->pool_id)
                    ->update([
                        'total_pool_fund' => $totalPoolFund,
                        'available_fund' => $availableFund,
                        'last_updated' => now(),
                        'updated_at' => now()
                    ]);
            } else {
                // Create a new poolfund record if it doesn't exist
                DB::table('poolfunds')->insert([
                    'pool_id' => $request->pool_id,
                    'total_pool_fund' => $request->investment_amount,
                    'available_fund' => $request->investment_amount,
                    'operating_fund' => 0.00,
                    'last_updated' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Commit the transaction
            DB::commit();

            return redirect()->back()
                ->with('success', "Your investment of " . number_format($request->investment_amount, 2) . " {$pool->currency} in {$pool->pool_name} has been successfully processed!");
        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process investment: ' . $e->getMessage());
        }
    }
}
