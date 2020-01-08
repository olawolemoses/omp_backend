<?php

namespace App\Http\Controllers\Admin;

use JWTAuth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;


class RegisterCtrl extends Controller
{
    /**
     * Creates and authenticates a new user
     *
     * @param Request $request
     * @return JsonResponse
     * @throws HttpException
     * @throws ValidationException
     */

     public function register (Request $request)
     {

      
         $this->validate($request, [
            'name'=> 'required|string|max:255',
            'email' => 'required| email|unique:admins',
            'phone' => 'required|string',
            'role_id' => 'required |int',
            'password' => 'required|confirmed|min:6',
            'shop_name' => 'required|string'
         ]);


         $admin = Admin::create([
            'name' =>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'role_id'=>$request->role_id,
            'password'=>Hash::make($request->password),
            'shop_name'=>$request->shop_name

         ]);

         if(!$admin)
         {
            return response()->json([
               'status' => true,
               'data' => 'Unable to create admin',
           ]);
         }
         else{
            return response()->json([
               'status' => true,
               'data' => [ 'admin' => $admin],
               'token' => JWTAuth::fromUser($admin),
           ], 201);
         }

       
     }
}