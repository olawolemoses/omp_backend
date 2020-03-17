<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\Admin;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Log;

class AdminAuth
{
    public function handle( $request, Closure $next,$guard = null)
    {
        $jwt = null;
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];

        Log::info($authHeader . " Mother Token");


        $arr =  preg_split("[Bearer]", $authHeader);
        
        
        if (isset($arr)) {
            $jwt = $arr[1];
        }       
        
        if(!$jwt) {
            // Unauthorized response if token not there
            return response()->json([
                'error' => 'Token not provided.',

            ], 401);
        }

        try {
            $credentials = JWT::decode($jwt, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json([
                'error' => 'Provided token is expired.',
                "error" => $e->getMessage()
            ], 400);
        } catch(Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.',
                "errors" =>$e->getMessage()
            ], 400);
        }

        $admin = Admin::find($credentials->sub);

        // Now let's put the admin in the request class so that you can grab it from there
        $request->auth = $admin;

        return $next($request);
    }
}