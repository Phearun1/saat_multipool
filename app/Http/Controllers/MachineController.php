<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MachineController extends Controller
{

    public function view_all_machine(Request $request)
    {
        $query = DB::table('machines');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('machine_id', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
        }

        $machines = $query->paginate(10);
        $users = DB::table('users')->select('user_id', 'full_name')->get();

        return view('pages.machine.view_all_machine', compact('machines', 'users'));
    }
    public function view_machine_detail($machine_id)
    {
        // Fetch machine details
        $machine = DB::table('machines')
            ->where('machine_id', $machine_id)
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

        // Fetch users for assigning profit distribution (including phone_number)
        $users = DB::table('users')
            ->join('operationalpartnerassignments', 'users.user_id', '=', 'operationalpartnerassignments.user_id')
            ->select('users.user_id', 'users.full_name', 'users.phone_number', 'operationalpartnerassignments.role', 'operationalpartnerassignments.percentage')
            ->where('operationalpartnerassignments.machine_id', $machine_id)
            ->get();

        // Pass data to the view
        return view('pages.machine.view_machine_detail', compact('machine', 'transactions', 'users'));
    }


    public function add_machine(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255',
            'installation_date' => 'required|date',
            'profit_share_investors' => 'required|numeric|min:0|max:100',
            'profit_share_operators' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:Active,Inactive,Maintenance',
            'address' => 'required|string',
            'image' => 'nullable|image|',
        ]);

        // Split the address into latitude and longitude
        $coordinates = explode(',', $request->address);
        if (count($coordinates) != 2) {
            return redirect()->back()->withErrors(['address' => 'Please enter valid coordinates in the format "latitude, longitude".']);
        }

        $latitude = trim($coordinates[0]);
        $longitude = trim($coordinates[1]);

        // Handle the image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('machines', 'public');
        } else {
            $imagePath = 'default/machine_default.png'; // Path to the default image
        }

        $machine_id = DB::table('machines')->insertGetId([
            'location' => $request->location,
            'installation_date' => $request->installation_date,
            'profit_share_investors' => $request->profit_share_investors,
            'profit_share_operators' => $request->profit_share_operators,
            'status' => $request->status,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'tds_level' => 0,
            'water_tank_status' => 0,
            'online_status' => 'Online',
            'total_revenue' => 0,
            'total_sales_volume' => 0,
            'bottles_saved_count' => 0,
            'image' => $imagePath
        ]);

        return redirect()->route('machines.all')->with('success', 'Machine added successfully.');
    }


    private function getRoleFromUserType($user_type)
    {
        switch ($user_type) {
            case 1:
                return 'Investor';
            case 2:
                return 'Space Owner';
            case 3:
                return 'Money Collector';
            case 4:
                return 'Maintenance';
            case 5:
                return 'Admin';
            default:
                return 'Unknown';
        }
    }
}
