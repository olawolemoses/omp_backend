<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChangePasswordCtrl extends Controller
{
    
    // use ResetsPasswords;

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        if (!Hash::check($request->old_password, $request->user()->getAuthPassword()))
            throw new HttpException(400, "Wrong password");

        if ($request->old_password === $request->password)
            throw new HttpException(400, "New password cannot be the same as old password");

        $this->resetPassword($request->user(), $request->password);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully',
        ], 200);
    }
}
