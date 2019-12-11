<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ChangePasswordCtrl extends Controller
{
    use ResetsPasswords;

    public function __construct()
    {
        $this->middleware('auth:api');
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
