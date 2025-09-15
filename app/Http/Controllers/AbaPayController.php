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
            // Get authenticated user's phone number
            $user = DB::table('users')->where('user_id', auth()->id())->first();
            if (!$user || empty($user->phone_number)) {
                return response()->json(['error' => true, 'message' => 'Phone number not found'], 400);
            }

            // Validate request data
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => true, 'message' => $validator->errors()->first()], 400);
            }

            // Generate a Unique Transaction ID using time()
            $transactionId = time(); //  Exactly what you requested!

            $amount = (float) $request->amount;
            $currency = 'USD'; // Normalize currency
            $saatmoneyUsername = 'QA.sandbox0012';
            $saatmoneyApiKey = 'KNg5QHZbOJrGnTgD';
            $returnUrl = 'https://pool.saat.cam/api/pushback';

            // Save transaction in database
            DB::beginTransaction();

            DB::table('wallet_transaction')->insert([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'transaction_id' => $transactionId, // Using `time()`
                'transaction_date' => now(),
                'type' => 'Deposit',
                'status' => 'Pending',
                'created_at' => now(),
            ]);

            DB::table('aba_pay')->insert([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'transaction_id' => $transactionId, // Using `time()`
                'currency' => $currency, // Store currency
                'status' => 0, // 0 = Pending
                'created_at' => now(),
            ]);

            DB::commit(); // Commit transaction if everything is successful

            // Return a full HTML page with an auto-submitting form
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
                <input type='hidden' name='currency' value='{$currency}'> <!-- Pass currency -->
                <input type='hidden' name='transaction_id' value='{$transactionId}'> <!-- Using `time()` -->
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

            // Validate amount and currency
            if ((float) $walletTransaction->amount !== $amount) {
                Log::warning("Transaction amount mismatch for {$transactionId}. Expected: {$walletTransaction->amount}, Received: {$amount}");
                return response()->json(['error' => 'Amount mismatch'], 400);
            }

            // Perform database updates in a transaction
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

            DB::commit(); // Commit only if updates are successful

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
