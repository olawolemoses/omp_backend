<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginController extends Controller
{
    /**
     * Authenticates an existing user
     *
     * @param Request $request
     * @return JsonResponse
     * @throws HttpException
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $this->validate($request, ['email' => 'required|email', 'password' => 'required']);

        $token = Auth::attempt($request->only(['email', 'password']));

        if (!$token) throw new HttpException("Invalid login credentials", 401);

        $user = User::whereEmail($request->email)->first();

        return response()->json([
            'success' => true,
            'data' => ['user' => $user],
            'token' => $this->getAuthTokenData($user, $token),
        ], 200);
    }
}
