<?php

namespace App\Strategies\Login;

use App\Contracts\AuthLoginInterface;
use App\Models\SocialAccount;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Auth;

class GoogleStrategy implements AuthLoginInterface
{
    public function login(Request $request) {
        $googleToken = $request->input('token');

        if (!$googleToken) {
            return response()->json(['error' => 'Token is required'], 400);
        }

        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);

        try {
            $payload = $client->verifyIdToken($googleToken);

            if (!$payload) {
                return response()->json(['error' => 'Invalid Google token'], 401);
            }

            $googleId = $payload['sub'];
            $emailAddress = $payload['email'];
            $firstName = $payload['given_name'];
            $lastName = $payload['family_name'];

            $user = User::where('email_address', $emailAddress)->first();

            if (!$user) {
                $user = User::create([
                    'email_address' => $emailAddress,
                    'customer_id' => date('ymdhis'),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email_verified_at' => Carbon::now(),
                ]);
            } else {
                $user->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ]);
            }

            Auth::login($user, true);

            $user = Auth::user();
            $token = $user->createToken(config('app.name'))->plainTextToken;

            if (SocialAccount::where('user_id', $user->id)->doesntExist()) {
                SocialAccount::create([
                    'user_id' => $user->id,
                    'provider' => 'google',
                    'provider_id' => $googleId
                ]);
            }

            return response()->json(['token' => $token]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}