<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookSenderController extends Controller
{
    public function sendTestWebhook()
    {
        $webhookUrl = url('/payment/catchback'); // Target your webhook receiver

        // ✅ Dummy test data (Simulating Saat Money Response)
        $testData = [
            "transaction_id" => "TXN" . time(),
            "amount" => 150.75,
            "currency" => "USD",
            "status" => "1", // 1 = Success, 0 = Failed
            "return_url_to_merchant" => "https://merchant.com/return"
        ];

        // ✅ Send POST request to webhook
        $response = Http::post($webhookUrl, $testData);

        // ✅ Log the response
        Log::info('Test Webhook Sent', [
            'request' => $testData,
            'response_status' => $response->status(),
            'response_body' => $response->body(),
        ]);

        return response()->json([
            'message' => 'Test webhook sent!',
            'request_data' => $testData,
            'response' => $response->json(),
        ]);
    }
}
