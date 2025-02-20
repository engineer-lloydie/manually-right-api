<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TokenAuthController extends Controller
{
    public function getUser() {
        return auth()->user();
    }

    public function register(Request $request) {
        try {
            $registerUserData = $request->validate([
                'first_name'=>'required|string',
                'last_name'=>'required|string',
                'email_address'=>'required|string|email|unique:users',
                'password'=>'required|min:8'
            ]);
    
            User::create([
                'customer_id' => date('ymdhis'),
                'first_name' => $registerUserData['first_name'],
                'last_name' => $registerUserData['last_name'],
                'email_address' => $registerUserData['email_address'],
                'password' => Hash::make($registerUserData['password']),
            ]);
    
            return response()->json([
                'message' => 'User Created'
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function login(Request $request) {
        try {
            $request->validate([
                'email_address'=>'required|string|email',
                'password'=>'required|min:8'
            ]);
    
            if (Auth::attempt($request->only('email_address', 'password'))) {
                // Generate a token using Sanctum
                $user = Auth::user();
                $token = $user->createToken(env('APP_NAME'))->plainTextToken;
        
                return response()->json(['token' => $token]);
            }
        
            return response()->json(['message' => 'Invalid credentials. Please input correct email and password.'], 400);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            "message"=>"logged out"
        ]);
    }
}
