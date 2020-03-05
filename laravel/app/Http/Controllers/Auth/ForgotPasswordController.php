<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
 /**
     * Request password reset email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function requestEmail(Request $request)
    {
        $this->broker()->sendResetLink($request->only('email'));

        // Use this to conditionally send a response where $response is the return value of the line above
        // if ($response === Password::RESET_LINK_SENT) {}

        return response()->json([
            'success' => true,
            'message' => 'A password reset email has been sent to the provided email',
        ], 200);
    }


    /**
     * Resets password
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'token' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => 'Password Reset Successfully',
            ], 200);
        }

        throw new HttpException(500, "Password could not be reset. Please try again");
    }

    public function broker()
    {
        return Password::broker();
    }


    public function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }
}
