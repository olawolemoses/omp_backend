<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
// use Firebase\JWT\JWT;

// use Firebase\JWT\ExpiredException;
use JWTAuth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Hash;
use Illuminate\Auth\Events\PasswordReset;

class LoginCtrl extends Controller
{
    use SendsPasswordResetEmails, ResetsPasswords {
        SendsPasswordResetEmails::broker insteadof ResetsPasswords;
    }
    
    public function _construct(Request $request) {
        $this->request = $request;
    }
    
    public function callResetPassword(Request $request)
    {
        return $this->reset($request);
    }

    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
        event(new PasswordReset($user));
    }

    public function sendPasswordResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }
    
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json([
            'message' => 'Password reset email sent.',
            'data' => $response
        ]);
    }    
    
    
    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Password reset successfully.']);
    }
    
    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Failed, Invalid Token.']);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Email could not be sent to this email address.']);
    }    

    
    public function authenticate(Request $request)
    {
        \Config::set('auth.providers.users.model', \App\Admin::class);

        $credentials = $request->only('email', 'password');
        $admin = Admin::whereEmail($request->email)->first();

        if($admin)
        {
            $hasher = app()->make('hash');

            $token = $hasher->check($request->input('password'), $admin->password);

            if($token && $admin) {

                return response()->json([
                    'status' => true,
                    'data' => [ 'admin' => $admin],
                    'token' => JWTAuth::fromUser($admin,[]),
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' => 'Incorrect Password',
                ]);
            }            
        }  
        else
        {
            return response()->json([
                'status' => false,
                'data' => 'Incorrect Password',
            ]);
        }         
        // return response()->json(compact('credentials'));



        // try {
        //     if (! $token = JWTAuth::attempt($credentials)) {
        //         return response()->json(['error' => 'invalid_credentials'], 400);
        //     }
        // } catch (JWTException $e) {
        //     return response()->json(['error' => 'could_not_create_token'], 500);
        // }

        return response()->json(compact('token'));
    }

     /**
     * Create a new token.
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(Admin $admin){
        $payload = [
            'iss' => "lumen-jwt", //Issuer of the token
            'sub' => $admin->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60*60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));        
    }


    public function Login (Request $request)
     {
        //Validate Credentials
        $this->validate($request, [
            'email'=>'required|email',
            'password'=>'required'
        ]);

        //hashing for the password 
        $hasher = app()->make('hash');

        //Check if email exist.
        $admin = Admin::whereEmail($request->email)->first();

        if($admin)
        {
            $token = $hasher->check($request->input('password'),$admin->password);

            if($token)
            {

                return response()->json([
                    'status' => true,
                    'data' => [ 'admin' => $admin],
                    'token' => $this->jwt($admin),
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' => 'Incorrect Password',
                ]);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'data' => 'Incorrect Email',
            ]);
        }


     }

     public function logout(Request $request)
     {

     }

}