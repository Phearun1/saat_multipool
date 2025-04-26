<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function createUser(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:100|unique:users',
            'full_name' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'user_type' => 'required|integer|in:1,2,3,4,5',
            'status' => 'required|integer|in:1,0',
        ]);
        DB::table('users')->insert([
            'username' => $request->input('username'),
            'password_hash' => Hash::make($request->input('password')),
            'email' => $request->input('email'),
            'full_name' => $request->input('full_name'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address'),
            'user_type' => $request->input('user_type'),
            'created_by' => auth()->id(),
            'date_joined' => now(),
            'status' => $request->input('status'),
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }


    public function updateUser(Request $request, $userId)
    {
        // Validate input data
        $validatedData = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $userId . ',user_id',
            'email' => 'required|string|email|max:100|unique:users,email,' . $userId . ',user_id',
            'full_name' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'user_type' => 'required|integer|in:1,2,3,4,5',
        ]);

        try {
            // Update user record in the database
            DB::table('users')
                ->where('user_id', $userId)
                ->update([
                    'username' => $validatedData['username'],
                    'email' => $validatedData['email'],
                    'full_name' => $validatedData['full_name'] ?? null,
                    'phone_number' => $validatedData['phone_number'] ?? null,
                    'address' => $validatedData['address'] ?? null,
                    'user_type' => $validatedData['user_type'],
                ]);

            return redirect()->back()->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function deleteUser($userId)
    {
        try {
            // Delete user from the database
            DB::table('users')->where('user_id', $userId)->delete();

            return redirect()->route('admin.user.details')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }




    // Display user list
    public function userList()
    {
        $users = DB::table('users')->paginate(10);
        return view('pages/admin/view_all_users', compact('users'));
    }


    // Handle AJAX search requests
    public function ajaxSearchUsers(Request $request)
    {
        $query = DB::table('users')->select('user_id', 'full_name', 'email', 'phone_number', 'user_type');

        if ($request->has('phone_number') && $request->phone_number) {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }

        $users = $query->get();

        return response()->json(['users' => $users]);
    }




    public function userDetails($id)
    {
        // Fetch user details
        $user = DB::table('users')
            ->where('user_id', $id)
            ->first();

        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found.');
        }

        // Define user roles
        $userTypes = [
            1 => 'Investor',
            2 => 'Space Owner',
            3 => 'Money Collector',
            4 => 'Maintenance',
            5 => 'Admin'
        ];

        // Fetch wallet and sponsor balances
        $balance = DB::table('balance')
            ->where('user_id', $id)
            ->select('total_available_fund as wallet_balance', 'portfolio_fund as sponsor_balance')
            ->first();

        // Fetch wallet transactions
        $walletTransactions = DB::table('wallet_transaction')
            ->where('user_id', $id)
            ->select('id', 'transaction_date', 'amount', 'type', 'status')
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Fetch sponsor transactions
        $sponsorTransactions = DB::table('sponsor_transaction')
            ->where('user_id', $id)
            ->select('id', 'transaction_date', 'total', 'type', 'status')
            ->orderBy('transaction_date', 'desc')
            ->get();

        return view('pages/admin/view_user_detail', compact('user', 'userTypes', 'balance', 'walletTransactions', 'sponsorTransactions'));
    }



    public function ViewAllMaintenanceRecords()
    {
        $records = DB::table('maintenancerecords')
            ->join('machines', 'maintenancerecords.machine_id', '=', 'machines.machine_id')
            ->join('users', 'maintenancerecords.user_id', '=', 'users.user_id')
            ->select(
                'maintenancerecords.*',
                'machines.location as machine_location',
                'machines.status as machine_status',
                'users.full_name as user_name',
                'users.email as user_email',
                'maintenancerecords.next_scheduled_maintenance as next_scheduled_maintenance'
            )
            ->orderBy('maintenancerecords.maintenance_date', 'desc')
            ->get();

        return view('pages.admin.view_all_maintenance_records', compact('records'));
    }


    public function viewallOperationalPartnerAccount()
    {
        $operationalpartneraccount = DB::table('operationalpartneraccounts')
            ->join('users', 'operationalpartneraccounts.user_id', '=', 'users.user_id')
            ->select(
                'operationalpartneraccounts.partner_account_id',
                'users.full_name as user_name',
                'users.email as user_email',
                'users.phone_number as user_phone_number',
                'operationalpartneraccounts.wallet_balance',
                'operationalpartneraccounts.last_updated'
            )
            ->orderBy('operationalpartneraccounts.last_updated', 'desc')
            ->get();

        return view('pages.admin.view_operational_partner_account', compact('operationalpartneraccount'));
    }

    
    public function viewOperationalPartnerAccountDetail($id)
    {
        $operationalpartneraccount = DB::table('operationalpartneraccounts')
            ->join('users', 'operationalpartneraccounts.user_id', '=', 'users.user_id')
            ->select(
                'operationalpartneraccounts.partner_account_id',
                'users.full_name as user_name',
                'users.email as user_email',
                'users.phone_number as user_phone_number',
                'operationalpartneraccounts.wallet_balance',
                'operationalpartneraccounts.last_updated'
            )
            ->where('operationalpartneraccounts.partner_account_id', $id)
            ->first();

        if (!$operationalpartneraccount) {
            dd("No record found for ID: " . $id, DB::getQueryLog());
        }

        $transactions = DB::table('operationalpartnertransactions')
            ->where('partner_account_id', $id)
            ->orderBy('transaction_date_time', 'desc')
            ->get();

        return view('pages.admin.view_operational_partner_account_detail', compact('operationalpartneraccount', 'transactions'));
    }

    public function viewProfitDistribution()
    {
        $profitDistribution = DB::table('profitdistribution')
            ->join('machinerevenuetransactions', 'profitdistribution.sale_id', '=', 'machinerevenuetransactions.sale_id')
            ->join('machines', 'machinerevenuetransactions.machine_id', '=', 'machines.machine_id')
            ->select(
                'profitdistribution.*',
                'machines.machine_id',
                'machines.location as machine_location',
                'machinerevenuetransactions.sale_date_time'
            )
            ->get();

        return view('pages.admin.view_all_profit_distribution', compact('profitDistribution'));
    }

        // Assign Profit to Users
        public function assignProfit(Request $request)
        {
            $request->validate([
                'machine_id' => 'required|integer',
                'percentages' => 'required|array',
                'percentages.*' => 'required|integer|min:0|max:100',
            ]);
    
            try {
                foreach ($request->percentages as $user_id => $percentage) {
                    DB::table('operationalpartnerassignments')->updateOrInsert(
                        ['user_id' => $user_id, 'machine_id' => $request->machine_id],
                        ['percentage' => $percentage]
                    );
                }
    
                return response()->json(['success' => true, 'message' => 'Profit distribution assigned successfully.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error assigning profit: ' . $e->getMessage()]);
            }
        }
    
        // Add User to Machine
        public function addUserProfit(Request $request)
        {
            $request->validate([
                'user_id' => 'required|exists:users,user_id',
                'machine_id' => 'required|exists:machines,machine_id',
            ]);
    
            try {
                DB::table('operationalpartnerassignments')->updateOrInsert(
                    ['user_id' => $request->user_id, 'machine_id' => $request->machine_id],
                    ['percentage' => 0] // Default profit percentage
                );
    
                return response()->json(['success' => true, 'message' => 'User linked to machine successfully.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error linking user: ' . $e->getMessage()]);
            }
        }
    
        // Search Users by Full Name or Phone Number
        public function searchUsers(Request $request)
        {
            $query = $request->input('query');
    
            $users = DB::table('users')
                ->where('full_name', 'like', "%$query%")
                ->orWhere('phone_number', 'like', "%$query%")
                ->select('user_id', 'full_name', 'phone_number', 'user_type')
                ->limit(10)
                ->get();
    
            return response()->json(['users' => $users]);
        }
    
        // Remove User from Machine Assignment
        public function deleteUserProfit($user_id, $machine_id)
        {
            try {
                DB::table('operationalpartnerassignments')
                    ->where('user_id', $user_id)
                    ->where('machine_id', $machine_id)
                    ->delete();
    
                return response()->json(['success' => true, 'message' => 'User removed from machine successfully.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error removing user: ' . $e->getMessage()]);
            }
        }
    
    


    public function viewNewLocationRequests()
    {
        // Fetch all location requests
        $requests = DB::table('location_requests')
            ->join('users', 'location_requests.user_id', '=', 'users.user_id')
            ->select('location_requests.*', 'users.full_name')
            ->orderBy('location_requests.created_at', 'desc')
            ->get();

        return view('pages.admin.view_new_location_requests', [
            'requests' => $requests,
        ]);
    }

    public function updateLocationRequestStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,Finished,Declined',
        ]);

        DB::table('location_requests')->where('id', $id)->update([
            'status' => $request->input('status'),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.view_new_location_requests')->with('success', 'Location request status updated successfully.');
    }

    public function deleteLocationRequest($id)
    {
        $locationRequest = DB::table('location_requests')->where('id', $id)->first();

        if (!$locationRequest) {
            return response()->json(['error' => 'Request not found.'], 404);
        }

        // Delete associated photos
        if ($locationRequest->location_photos) {
            $photos = json_decode($locationRequest->location_photos, true);
            foreach ($photos as $photo) {
                $photoPath = public_path('storage/' . $photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath); // Remove the photo using unlink
                }
            }
        }

        // Delete the request from the database
        DB::table('location_requests')->where('id', $id)->delete();

        return redirect()->route('admin.view_new_location_requests')->with('success', 'Request deleted successfully.');
    }
    public function viewAllAccountReferralRequests(Request $request)
    {
        $query = DB::table('users')
            ->where('created_by', auth()->id())
            ->orderBy('date_joined', 'desc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('full_name', 'like', '%' . $search . '%');
            });
        }

        $referrals = $query->get();

        $userTypes = [
            1 => 'Investor',
            2 => 'Space Owner',
            3 => 'Money Collector',
            4 => 'Maintenance',
            5 => 'Admin'
        ];

        return view('pages.admin.view_all_account_referral_request', [
            'referrals' => $referrals,
            'userTypes' => $userTypes,
        ]);
    }

    public function approveReferral($id)
    {
        DB::table('users')->where('user_id', $id)->update(['status' => 1]);
        return redirect()->route('admin.view_all_account_referral_requests')->with('success', 'Referral approved successfully.');
    }

    public function rejectReferral($id)
    {
        DB::table('users')->where('user_id', $id)->update(['status' => 2]);
        return redirect()->route('admin.view_all_account_referral_requests')->with('success', 'Referral rejected successfully.');
    }
}
