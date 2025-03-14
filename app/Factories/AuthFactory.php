<?php

namespace App\Factories;

use App\Strategies\Login\EmailPasswordStrategy;
use App\Strategies\Login\GoogleStrategy;
use Exception;
use Illuminate\Support\Facades\App;

class AuthFactory
{
    public static function create($method) {
        return match ($method) {
            'email_password' => App::make(EmailPasswordStrategy::class),
            'google' => App::make(GoogleStrategy::class),
            default => throw new Exception("Invalid Auth Method"),
        };
    }
}