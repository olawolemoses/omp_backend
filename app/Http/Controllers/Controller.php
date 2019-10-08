<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    protected function getAuthTokenData(User $user, string $token = null)
    {
        return [
            'token' => $token ?? JWTAuth::fromUser($user),
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
        ];
    }
}
