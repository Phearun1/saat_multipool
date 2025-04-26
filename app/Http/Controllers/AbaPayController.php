<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Laravel\Ui\Presets\React;

class AbaPayController extends Controller
{
    public function deposit(Request $request)
    {
        try {
            // ✅ Get authenticated user's phone number
            $user = DB::table('users')->where('user_id', auth()->id())->first();
            if (!$user || empty($user->phone_number)) {
                return response()->json(['error' => true, 'message' => 'Phone number not found'], 400);
            }

            // ✅ Validate request data
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => true, 'message' => $validator->errors()->first()], 400);
            }

            // ✅ Generate a Unique Transaction ID using time()
            $transactionId = time(); // 🔥 Exactly what you requested!

            $amount = (float) $request->amount;
            $currency = 'USD'; // Normalize currency
            $saatmoneyUsername = 'QA.sandbox0012';
            $saatmoneyApiKey = 'KNg5QHZbOJrGnTgD';
            $returnUrl = 'https://pool.saat.cam/api/push_back';

            // ✅ Save transaction in database
            DB::beginTransaction();

            DB::table('wallet_transaction')->insert([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'transaction_id' => $transactionId, // ✅ Using `time()`
                'transaction_date' => now(),
                'type' => 'Deposit',
                'status' => 'Pending',
                'created_at' => now(),
            ]);

            DB::table('aba_pay')->insert([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'transaction_id' => $transactionId, // ✅ Using `time()`
                'currency' => $currency, // ✅ Store currency
                'status' => 0, // 0 = Pending
                'created_at' => now(),
            ]);

            DB::commit(); // ✅ Commit transaction if everything is successful

            // ✅ Return a full HTML page with an auto-submitting form
            return response()->make(
                "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=no'>
            <title>Processing Payment...</title>
            <style>
                html, body { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; display: flex; justify-content: center; align-items: center; }
                .loading { font-family: Arial, sans-serif; font-size: 18px; color: #333; }
            </style>
        </head>
        <body>
            <p class='loading'>Redirecting to payment...</p>
            <form id='paymentForm' action='https://saatmoney.com/payway/checkout/saatmoney_payment_khqr.php' method='POST'>
                <input type='hidden' name='tag' value='payway'>
                <input type='hidden' name='username' value='{$saatmoneyUsername}'>
                <input type='hidden' name='saat_api_key' value='{$saatmoneyApiKey}'>
                <input type='hidden' name='amount' value='{$amount}'>
                <input type='hidden' name='phone_user' value='{$user->phone_number}'>
                <input type='hidden' name='currency' value='{$currency}'> <!-- ✅ Pass currency -->
                <input type='hidden' name='transaction_id' value='{$transactionId}'> <!-- ✅ Using `time()` -->
                <input type='hidden' name='return_url_to_merchant' value='{$returnUrl}'>
            </form>
            <script>
                document.getElementById('paymentForm').submit();
            </script>
        </body>
        </html>",
                200,
                ['Content-Type' => 'text/html']
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Deposit Error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => true, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function pushback(Request $request)
    {
        try {
            $data = $request->all();

            // Extract data from request
            $transactionId = $data['transaction_id'];
            $amount = (float) $data['amount'];
            $currency = strtoupper(trim($data['currency']));
            $status = $data['status']; // "1" means successful

            // Check if transaction exists in wallet_transaction table
            $walletTransaction = DB::table("wallet_transaction")->where('transaction_id', $transactionId)->first();
            if (!$walletTransaction) {
                return response()->json(['error' => 'Transaction not found in wallet_transaction'], 404);
            }

            // ✅ Validate amount and currency
            if ((float) $walletTransaction->amount !== $amount) {
                Log::warning("Transaction amount mismatch for {$transactionId}. Expected: {$walletTransaction->amount}, Received: {$amount}");
                return response()->json(['error' => 'Amount mismatch'], 400);
            }

            // ✅ Perform database updates in a transaction
            DB::beginTransaction();

            // Update wallet_transaction status to 'Completed' if status is "1"
            if ($status == 1) {
                DB::table("wallet_transaction")->where('transaction_id', $transactionId)->update([
                    'status' => 'Completed',
                    'updated_at' => now(),

                ]);
                DB::table("aba_pay")->where('transaction_id', $transactionId)->update([
                    'status' => 1,
                ]);

                // Update the total available fund in the balance table
                DB::table('balance')
                    ->where('user_id', $walletTransaction->user_id)
                    ->increment('total_available_fund', $amount); // Increase the balance by the deposit amount
            }

            DB::commit(); // ✅ Commit only if updates are successful

            // Log successful transaction update
            Log::info("Transaction {$transactionId} successfully updated in wallet_transaction.", $data);

            return response()->json(['message' => 'Transaction updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Pushback Error:", ['message' => $e->getMessage()]);
            return response()->json(['error' => true, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }


} 
        


    // public function pushback(Request $request)
    // {
    //     try {
    //         // Decode JSON input from the request
    //         $data = json_decode($request->getContent(), true);

    //         // Validate required fields
    //         if (!isset($data['transaction_id'], $data['amount'], $data['currency'], $data['status'])) {
    //             return response()->json(['error' => 'Missing required fields (transaction_id, amount, currency, status)'], 400);
    //         }

    //         // Extract data from request
    //         $transactionId = $data['transaction_id'];
    //         $amount = (float) $data['amount'];
    //         $currency = strtoupper(trim($data['currency']));
    //         $status = $data['status']; // "1" means successful
    //         $returnUrl = $data['return_url_to_merchant'] ?? null;

    //         // Check if transaction exists in aba_pay table
    //         $transaction = DB::table("aba_pay")->where('transaction_id', $transactionId)->first();
    //         if (!$transaction) {
    //             return response()->json(['error' => 'Transaction not found in aba_pay'], 404);
    //         }

    //         // Check if transaction exists in wallet_transaction table
    //         $walletTransaction = DB::table("wallet_transaction")->where('transaction_id', $transactionId)->first();
    //         if (!$walletTransaction) {
    //             return response()->json(['error' => 'Transaction not found in wallet_transaction'], 404);
    //         }

    //         // ✅ Validate amount and currency
    //         if ((float) $transaction->amount !== $amount || (float) $walletTransaction->amount !== $amount) {
    //             Log::warning("Transaction amount mismatch for {$transactionId}. Expected: {$transaction->amount}, Received: {$amount}");
    //             return response()->json(['error' => 'Amount mismatch'], 400);
    //         }

    //         if ($currency !== strtoupper(trim($transaction->currency))) {
    //             Log::warning("Transaction currency mismatch for {$transactionId}. Expected: {$transaction->currency}, Received: {$currency}");
    //             return response()->json(['error' => 'Currency mismatch'], 400);
    //         }

    //         // ✅ Perform database updates in a transaction
    //         DB::beginTransaction();

    //         // Update aba_pay status
    //         DB::table("aba_pay")->where('transaction_id', $transactionId)->update([
    //             'status' => $status,
    //             'updated_at' => now(),
    //         ]);

    //         // Update wallet_transaction status to 'Completed'
    //         DB::table("wallet_transaction")->where('transaction_id', $transactionId)->update([
    //             'status' => 'Completed',
    //             'updated_at' => now(),
    //         ]);

    //         // If the transaction is a successful deposit, update the user's wallet balance
    //         if ($status == 1 && $walletTransaction->type == 'Deposit') {
    //             // Update the total available fund in the balance table
    //             DB::table('balance')
    //                 ->where('user_id', $walletTransaction->user_id)
    //                 ->increment('total_available_fund', $amount); // Increase the balance by the deposit amount
    //         }

    //         DB::commit(); // ✅ Commit only if both updates are successful

    //         // Log successful transaction update
    //         Log::info("Transaction {$transactionId} successfully updated in aba_pay and wallet_transaction.", $data);

    //         return response()->json(['message' => 'Transaction updated successfully'], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Error updating transaction: " . $e->getMessage());
    //         return response()->json(['error' => 'Internal server error'], 500);
    //     }
    // }
