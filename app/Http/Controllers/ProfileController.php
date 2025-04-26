<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    public function view_profile()
    {
        $user = Auth::user(); // Get the authenticated user
        return view('pages.profile.view_profile', compact('user'));
    }


    public function update_profile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        // Update user data
        $user->update([
            'phone_number' => $request->phone ?? $user->phone_number,
            'email' => $request->email ?? $user->email,
            'address' => $request->address ?? $user->address,
        ]);

        return redirect()->route('profile.view')->with('success', 'Profile updated successfully.');
    }


    public function viewReferral()
    {
        // Fetch all users where 'created_by' matches the current user's ID
        $referrals = DB::table('users')
            ->where('created_by', Auth::id())
            ->orderBy('date_joined', 'desc')
            ->get();

        // User types for display
        $userTypes = [
            1 => 'Investor',
            2 => 'Space Owner',
            3 => 'Money Collector',
            4 => 'Maintenance',
        ];

        return view('pages.profile.create_referral', compact('referrals', 'userTypes'));
    }

    /**
     * Handle the creation of a new user account.
     */
    public function createReferral(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'full_name' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'user_type' => 'required|integer|in:1,2,3,4',
        ]);

        // Insert the new user into the database
        DB::table('users')->insert([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'user_type' => $request->user_type,
            'created_by' => Auth::id(), // Associate the user with the creator
            'date_joined' => now(),
            'status' => '0',
        ]);

        return redirect()->route('view_referral')->with('success', 'Account created successfully.');
    }
}

