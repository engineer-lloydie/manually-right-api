<?php

namespace App\Services\Auth;

use App\Contracts\AuthLoginInterface;
use Exception;
use Illuminate\Http\Request;

class LoginService
{
    public function login(Request $request, AuthLoginInterface $authStrategy) {
        try {
            return $authStrategy->login($request);
        } catch (Exception $e) {
            throw $e;
        }
    }
}