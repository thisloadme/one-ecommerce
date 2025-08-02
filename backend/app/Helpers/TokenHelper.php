<?php

namespace App\Http\Controllers;

use App\Models\UserToken;
use Str;

class TokenHelper
{
    public static function generateToken($userId)
    {
        $tokenString = Str::random(32);
        $currentUserToken = UserToken::query()
            ->where('user_id', $userId)
            ->where('expires_at', '>', now())
            ->first();

        if ($currentUserToken) {
            return $currentUserToken->token;
        } else {
            $userToken = new UserToken();
            $userToken->user_id = $userId;
            $userToken->token = $tokenString;
            $userToken->expires_at = now()->addDays(2);
            $userToken->save();
        }

        return $tokenString;
    }

    public static function invalidateToken($token) 
    {
        $userToken = UserToken::query()->where('token', $token)->first();
        if ($userToken) {
            $userToken->expires_at = now();
            $userToken->save();
        }

        return true;
    }
}