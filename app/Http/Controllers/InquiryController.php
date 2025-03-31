<?php

namespace App\Http\Controllers;

use App\Http\Requests\InquiryRequest;
use App\Models\Inquiry;
use Exception;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function sendMessage(InquiryRequest $request)
    {
        try {
            // Inquiry::create([
            //     'name' => $request->firstName,
            //     'email' => $request->email,
            //     'message' => $request->message,
            // ]);

            // Send email to admin

            return response()->json(['message' => 'Inquiry sent successfully!'], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
