<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function register(Request $request) {
        User::create([
            'customer_id' => date('ymdhis'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email_address' => $request->input('email_address'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'message' => 'User Created'
        ]);
    }
}