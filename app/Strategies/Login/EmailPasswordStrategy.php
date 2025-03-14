<?php

namespace App\Strategies\Login;

use App\Contracts\AuthLoginInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailPasswordStrategy implements AuthLoginInterface
{
    public function login(Request $request) {
        try {
            $request->validate([
                'email_address'=>'required|string|email',
                'password'=>'required|min:8'
            ]);
    
            if (Auth::attempt($request->only('email_address', 'password'))) {
                // Generate a token using Sanctum
                $user = Auth::user();
                $token = $user->createToken(config('app.name'))->plainTextToken;
        
                return response()->json(['token' => $token]);
            }
        
            return response()->json(
                [
                    'message' => 'Invalid credentials. Please input correct email and password.'
                ], 400
            );
        } catch (Exception $e) {
            throw $e;
        }
    }
}
