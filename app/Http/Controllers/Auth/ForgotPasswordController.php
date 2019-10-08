<?php

namespace App\Http\Controllers\Auth;

use App\Mails\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function requestEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $user = User::whereEmail($request->email)->first();

        if ($user) {
            $user->update(['remember_token' => md5(Str::random(10))]);

            // Send email notification
            Mail::to($user)->send(new PasswordReset($user));
        }

        return response()->json([
            'success' => true,
            'message' => 'A password reset email has been sent to the provided email',
        ], 200);
    }
}
