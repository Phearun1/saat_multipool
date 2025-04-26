<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SponsorController extends Controller
{
    public function view_sponsor_account()
    {
        $sponsor = DB::table('balance')
            ->where('user_id', auth()->id())
            ->first();

        $transactions = DB::table('sponsor_transaction')
            ->where('user_id', auth()->id())
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        return view('pages.sponsor.view_sponsor_account', [
            'sponsor' => $sponsor,
            'transactions' => $transactions,
        ]);
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $userId = auth()->id();
        $amount = $request->input('amount');

        // Fetch user balance details (portfolio_fund and total_available_fund)
        $balance = DB::table('balance')->where('user_id', $userId)->first();

        // Check if the portfolio_fund is less than the withdrawal amount
        if (!$balance || $balance->portfolio_fund < $amount) {
            // If portfolio_fund is less than the amount, set the status as Pending
            DB::table('sponsor_transaction')->insert([
                'user_id' => $userId,
                'transaction_date' => now(),
                'total' => $amount,
                'type' => 'Withdraw',
                'status' => 'Pending',  // Status set to Pending
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('investment.sponsor_account')->with('success', 'Withdrawal is pending due to insufficient funds.');
        }

        // Start a transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Deduct from portfolio_fund and add to total_available_fund
            DB::table('balance')
                ->where('user_id', $userId)
                ->update([
                    'portfolio_fund' => $balance->portfolio_fund - $amount,
                    'total_available_fund' => $balance->total_available_fund + $amount,
                ]);

            // Deduct the same amount from total_pool_fund in the poolfunds table
            DB::table('poolfunds')
                ->decrement('total_pool_fund', $amount);

            // Save the withdrawal transaction with 'Pending' status
            DB::table('sponsor_transaction')->insert([
                'user_id' => $userId,
                'transaction_date' => now(),
                'total' => $amount,
                'type' => 'Withdraw',
                'status' => 'Completed', // Status set to Pending
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        

            // Commit the transaction
            DB::commit();

            return redirect()->route('investment.sponsor_account')->with('success', 'Withdrawal submitted for approval.');
        } catch (\Exception $e) {
            // Rollback transaction if an error occurs
            DB::rollBack();
            return redirect()->route('investment.sponsor_account')->with('error', 'An error occurred. Please try again later.');
        }
    }



    public function sponsor(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $userId = auth()->id();
        $amount = $request->input('amount');

        // Fetch user balance details (total_available_fund and portfolio_fund)
        $balance = DB::table('balance')->where('user_id', $userId)->first();

        // Check if there is enough available funds for the sponsorship
        if (!$balance || $balance->total_available_fund < $amount) {
            // If insufficient funds, mark transaction status as 'Pending'
            DB::table('sponsor_transaction')->insert([
                'user_id' => $userId,
                'transaction_date' => now(),
                'total' => $amount,
                'type' => 'Sponsor',
                'status' => 'Pending',  // Status set to pending
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('investment.sponsor_account')->with('success', 'Sponsorship request is pending due to insufficient funds.');
        }

        // Proceed only if funds are available
        DB::beginTransaction();

        try {
            // Deduct from total_available_fund and add to portfolio_fund
            DB::table('balance')
                ->where('user_id', $userId)
                ->update([
                    'total_available_fund' => $balance->total_available_fund - $amount,
                    'portfolio_fund' => $balance->portfolio_fund + $amount,
                ]);

            // Add the same amount to total_pool_fund in the poolfunds table
            DB::table('poolfunds')
                ->increment('total_pool_fund', $amount);

            // Save the transaction
            DB::table('sponsor_transaction')->insert([
                'user_id' => $userId,
                'transaction_date' => now(),
                'total' => $amount,
                'type' => 'Sponsor',
                'status' => 'Completed',  // Mark status as completed after processing
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Commit the transaction
            DB::commit();

            return redirect()->route('investment.sponsor_account')->with('success', 'Sponsorship successfully processed.');
        } catch (\Exception $e) {
            // Rollback transaction if an error occurs
            DB::rollBack();
            return redirect()->route('investment.sponsor_account')->with('error', 'An error occurred. Please try again later.');
        }
    }
}
