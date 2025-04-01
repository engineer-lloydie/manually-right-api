<?php

namespace App\Http\Controllers;

use App\Http\Requests\InquiryRequest;
use App\Mail\InquiryMail;
use App\Models\Inquiry;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InquiryController extends Controller
{
    public function sendMessage(InquiryRequest $request)
    {
        try {
            $filePath = 'icons/partial-logo.png';
            $expiry = now()->addMinutes(15);
            $url = Storage::temporaryUrl($filePath, $expiry);

            Mail::to('manuallyright@gmail.com')->send(new InquiryMail([
                'first_name' => $request->input('firstName'),
                'email' => $request->input('email'),
                'message' => $request->input('message'),
                'logo_url' => $url
            ]));
            
            Inquiry::create([
                'first_name' => $request->firstName,
                'email' => $request->email,
                'message' => $request->message,
            ]);

            return response()->json(['message' => 'Inquiry sent successfully!'], 200);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
