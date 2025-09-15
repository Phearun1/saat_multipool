<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MachineController extends Controller
{

    public function view_all_machine(Request $request)
    {
        $query = DB::table('machines');

        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('machine_id', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        // Get machines with pagination
        $machines = $query->paginate(10);

        // Get active pools for the dropdown
        $pools = DB::table('investment_pools')
            ->where('status', 'Active')
            ->select('pool_id', 'pool_name')
            ->orderBy('pool_name')
            ->get();

        return view('pages.machine.view_all_machine', compact('machines', 'pools'));
    }

    public function view_machine_detail($machine_id)
    {
        // Fetch machine details with pool information
        $machine = DB::table('machines')
            ->leftJoin('investment_pools', 'machines.pool_id', '=', 'investment_pools.pool_id')
            ->select('machines.*', 'investment_pools.pool_name')
            ->where('machines.machine_id', $machine_id)
            ->first();

        // Check if the machine exists
        if (!$machine) {
            return redirect()->route('machines.all')->withErrors(['error' => 'Machine not found']);
        }

        // Fetch transactions for the machine
        $transactions = DB::table('machinerevenuetransactions')
            ->where('machine_id', $machine_id)
            ->orderBy('sale_date_time', 'desc')
            ->get();

        // Fetch users for assigning profit distribution
        $users = DB::table('users')
            ->join('operationalpartnerassignments', 'users.user_id', '=', 'operationalpartnerassignments.user_id')
            ->select(
                'users.user_id',
                'users.full_name',
                'operationalpartnerassignments.role',
                'operationalpartnerassignments.percentage',
                'operationalpartnerassignments.comment'
            )
            ->where('operationalpartnerassignments.machine_id', $machine_id)
            ->get();

        // Get pools for the dropdown
        $pools = DB::table('investment_pools')
            ->where('status', 'Active')
            ->select('pool_id', 'pool_name')
            ->orderBy('pool_name')
            ->get();

        // Pass data to the view
        return view('pages.machine.view_machine_detail', compact('machine', 'transactions', 'users', 'pools'));
    }


    public function add_machine(Request $request)
    {
        $request->validate([
            'pool_id' => 'nullable|exists:investment_pools,pool_id',
            'location' => 'required|string|max:255',
            'installation_date' => 'required|date',
            'profit_share_investors' => 'required|numeric|min:0|max:100',
            'profit_share_operators' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:Active,Inactive,Maintenance',
            'address' => 'required|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Validate that profit shares total exactly 100%
        $totalProfitShare = $request->profit_share_investors + $request->profit_share_operators;

        if ($totalProfitShare != 100) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'profit_share_investors' => 'Profit shares must total exactly 100%.',
                    'profit_share_operators' => "Current total: {$totalProfitShare}%. Investors: {$request->profit_share_investors}%, Operators: {$request->profit_share_operators}%."
                ]);
        }

        // Split the address into latitude and longitude
        $coordinates = explode(',', $request->address);
        if (count($coordinates) != 2) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['address' => 'Please enter valid coordinates in the format "latitude, longitude".']);
        }

        $latitude = trim($coordinates[0]);
        $longitude = trim($coordinates[1]);

        // Validate coordinates are numeric
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['address' => 'Coordinates must be valid numbers.']);
        }

        // Handle the image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('machines', 'public');
        } else {
            $imagePath = 'saat_pool.png'; // Path to the default image
        }

        try {
            $machine_id = DB::table('machines')->insertGetId([
                'pool_id' => $request->pool_id ?: null, // Ensure null if empty
                'location' => $request->location,
                'installation_date' => $request->installation_date,
                'profit_share_investors' => $request->profit_share_investors,
                'profit_share_operators' => $request->profit_share_operators,
                'status' => $request->status,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'tds_level' => 0,
                'water_tank_status' => 'Good', // Set default status
                'online_status' => 'Online',
                'total_revenue' => 0.00,
                'total_sales_volume' => 0.00,
                'bottles_saved_count' => 0,
                'image' => $imagePath
            ]);

            return redirect()->route('machines.all')
                ->with('success', "Machine added successfully with ID #{$machine_id}. Profit distribution: {$request->profit_share_investors}% investors, {$request->profit_share_operators}% operators.");
        } catch (\Exception $e) {
            // If there was an error and we uploaded an image, delete it
            if ($imagePath && $imagePath !== 'saat_pool.png') {
                $fullImagePath = storage_path('app/public/' . $imagePath);
                if (file_exists($fullImagePath)) {
                    unlink($fullImagePath);
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add machine: ' . $e->getMessage());
        }
    }

    public function updateMachine(Request $request, $machine_id)
{
    $request->validate([
        'pool_id' => 'nullable|exists:investment_pools,pool_id',
        'location' => 'required|string|max:255',
        'installation_date' => 'required|date',
        'profit_share_investors' => 'required|numeric|min:0|max:100',
        'profit_share_operators' => 'required|numeric|min:0|max:100',
        'status' => 'required|string|in:Active,Inactive,Maintenance',
        'address' => 'required|string',
        'image' => 'nullable|image|max:2048', // Max 2MB
        'delete_image' => 'nullable|boolean',
    ]);

    // Validate that profit shares total exactly 100%
    $totalProfitShare = $request->profit_share_investors + $request->profit_share_operators;
    
    if ($totalProfitShare != 100) {
        return redirect()->back()
            ->withInput()
            ->withErrors([
                'profit_share_investors' => 'Profit shares must total exactly 100%.',
                'profit_share_operators' => "Current total: {$totalProfitShare}%. Investors: {$request->profit_share_investors}%, Operators: {$request->profit_share_operators}%."
            ]);
    }

    // Split the address into latitude and longitude
    $coordinates = explode(',', $request->address);
    if (count($coordinates) != 2) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['address' => 'Please enter valid coordinates in the format "latitude, longitude".']);
    }

    $latitude = trim($coordinates[0]);
    $longitude = trim($coordinates[1]);

    // Validate coordinates are numeric
    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['address' => 'Coordinates must be valid numbers.']);
    }

    // Get the existing machine data
    $existingMachine = DB::table('machines')->where('machine_id', $machine_id)->first();

    if (!$existingMachine) {
        return redirect()->route('machines.all')
            ->with('error', 'Machine not found.');
    }

    try {
        $imagePath = $existingMachine->image;

        // Handle image deletion
        if ($request->has('delete_image') && $request->delete_image) {
            // Delete the existing image if it's not the default
            if ($existingMachine->image && $existingMachine->image !== 'saat_pool.png') {
                $fullImagePath = storage_path('app/public/' . $existingMachine->image);
                if (file_exists($fullImagePath)) {
                    unlink($fullImagePath);
                }
            }
            $imagePath = 'saat_pool.png'; // Set to default image
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete the old image if it's not the default
            if ($existingMachine->image && $existingMachine->image !== 'saat_pool.png') {
                $fullImagePath = storage_path('app/public/' . $existingMachine->image);
                if (file_exists($fullImagePath)) {
                    unlink($fullImagePath);
                }
            }
            $imagePath = $request->file('image')->store('machines', 'public');
        }

        // Update the machine record
        DB::table('machines')->where('machine_id', $machine_id)->update([
            'pool_id' => $request->pool_id ?: null, // Ensure null if empty
            'location' => $request->location,
            'installation_date' => $request->installation_date,
            'profit_share_investors' => $request->profit_share_investors,
            'profit_share_operators' => $request->profit_share_operators,
            'status' => $request->status,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'image' => $imagePath
        ]);

        return redirect()
            ->route('machines.detail', $machine_id)
            ->with('success', "Machine updated successfully. Profit distribution: {$request->profit_share_investors}% investors, {$request->profit_share_operators}% operators.");

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update machine: ' . $e->getMessage());
    }
}


}
