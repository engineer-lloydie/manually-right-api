<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function createOrder(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $orderData = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $request->input('totalPrice') // Adjust the amount as needed
                    ]
                ]
            ]
        ];

        $order = $provider->createOrder($orderData);

        if (isset($order['id'])) {
            return response()->json(['orderID' => $order['id']]);
        }

        return response()->json(['error' => 'Could not create order'], 500);
    }

    /**
     * Capture an approved PayPal order.
     */
    public function captureOrder(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $orderID = $request->input('orderID');
        $result = $provider->capturePaymentOrder($orderID);

        if (isset($result['status']) && $result['status'] === 'COMPLETED') {
            // Update your database, notify user, etc.
            return response()->json(['status' => 'Payment captured successfully']);
        }

        return response()->json(['error' => 'Payment capture failed'], 500);
    }
}
