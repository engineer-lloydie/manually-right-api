<?php

namespace App\Http\Controllers\Auth;

use App\Factories\AuthFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Auth\LoginService;
use App\Services\Auth\RegisterService;
use AWS\CRT\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TokenAuthController extends Controller
{
    public function getUser() {
        return auth()->user();
    }

    public function register(RegisterRequest $request, RegisterService $registerService) {
        try {
            return $registerService->register($request);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function login(Request $request, LoginService $loginService) {
        try {
            $authStrategy = AuthFactory::create($request->input('auth_method'));
            return $loginService->login($request, $authStrategy);
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
