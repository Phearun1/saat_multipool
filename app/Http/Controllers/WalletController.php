<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function wallet()
    {
        // Fetch wallet details for the authenticated user
        $wallet = DB::table('balance')
            ->where('user_id', auth()->id())
            ->first();

        // Fetch the latest transactions with pagination (10 per page)
        $transactions = DB::table('wallet_transaction')
            ->where('user_id', auth()->id())
            ->orderBy('transaction_date', 'desc')
            ->paginate(10); // Paginate with 10 items per page

        return view('pages.wallet.view_wallet', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }

    public function checkTransactionStatus($transactionId)
    {
        // Fetch the wallet transaction
        $transaction = DB::table('wallet_transaction')->where('transaction_id', $transactionId)->first();

        // If the transaction doesn't exist, return an error
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found.'], 404);
        }

        // Check if the transaction is completed
        if ($transaction->status === 'Completed') {
            // If completed, update the balance
            DB::table('balance')
                ->where('user_id', $transaction->user_id)
                ->increment('total_available_fund', $transaction->amount);

            // Return the updated transaction status
            return response()->json([
                'status' => 'Completed',
                'message' => 'Transaction completed and balance updated.'
            ]);
        }

        // If not completed, just return the current status
        return response()->json([
            'status' => $transaction->status,
            'message' => 'Transaction is still pending.'
        ]);
    }

    public function getBalance()
    {
        // Fetch the wallet details for the authenticated user
        $wallet = DB::table('balance')
            ->where('user_id', auth()->id())
            ->first();

        // Return the balance as JSON response
        return response()->json([
            'balance' => number_format($wallet->total_available_fund ?? 0, 2) // Return the balance formatted
        ]);
    }


    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $userId = auth()->id();
        $amount = $request->input('amount');

        // Insert the transaction with a "Pending" status
        DB::table('wallet_transaction')->insert([
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'Deposit',
            'status' => 'Pending', // Status is now "Pending"
            'transaction_date' => now(),
        ]);

        return redirect()->route('wallet')->with('success', 'Deposit submitted for approval.');
    }


    public function withdraw(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);


        $userId = auth()->id();
        $amount = $request->input('amount');

        // Fetch the current balance
        $wallet = DB::table('balance')->where('user_id', $userId)->first();

        if (!$wallet || $wallet->total_available_fund < $amount) {
            return redirect()->route('wallet')->with('error', 'Insufficient balance.');
        }

        try {
            // Insert the transaction with a "Pending" status
            DB::table('wallet_transaction')->insert([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => 'Withdraw',
                'status' => 'Pending', // Status is now "Pending"
                'transaction_date' => now(),
            ]);

            return redirect()->route('wallet')->with('success', 'Withdrawal submitted for approval.');
        } catch (\Exception $e) {
            return redirect()->route('wallet')->with('error', 'Failed to process withdrawal. Please try again.');
        }
    }
}
