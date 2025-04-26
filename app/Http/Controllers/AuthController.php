<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        // Fetch user using phone_number
        $user = DB::table('users')
            ->where('phone_number', $request->phone_number)
            ->first();

        // Verify user and password
        if ($user && Hash::check($request->password, $user->password_hash)) {
            // Check if the user's account is active
            if ($user->status == 1) {
                // Use Laravel's Auth system to log the user in
                Auth::loginUsingId($user->user_id);
                // Redirect to the index page
                return redirect('/');
            } else if ($user->status == 2) {
                // Return with error if the account is not active
                return back()->withErrors(['phone_number' => 'Account is rejected by the admin!'])->onlyInput('phone_number');
            } else {
                // Return with error if the account is not active
                return back()->withErrors(['phone_number' => 'Account needs to be approved by the admin!'])->onlyInput('phone_number');
            }
        }

        // Return with error on failure
        return back()->withErrors(['phone_number' => 'Invalid phone number or password'])->onlyInput('phone_number');
    }




    // Handle logout
    public function logout(Request $request)
    {
        // Clear session data
        $request->session()->flush();

        return redirect('/login');
    }
}
