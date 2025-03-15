<?php

namespace App\Strategies\Login;

use App\Contracts\AuthLoginInterface;
use App\Models\User;
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

            $userRole = User::where('email_address', $request->email_address)->first()->role;

            if ($request->has('source') && $request->source == 'admin') {
                if ($userRole != 'admin') {
                    return $this->setErrorResponse();
                }
            } else {
                if ($userRole != 'user') {
                    return $this->setErrorResponse();
                }
            }
    
            if (Auth::attempt($request->only('email_address', 'password'))) {
                // Generate a token using Sanctum
                $user = Auth::user();
                $token = $user->createToken(config('app.name'))->plainTextToken;
        
                return response()->json(['token' => $token]);
            }
        
            return $this->setErrorResponse();
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function setErrorResponse() {
        return response()->json(
            [
                'message' => 'Invalid credentials. Please input correct email and password.'
            ], 400
        );
    }
}
