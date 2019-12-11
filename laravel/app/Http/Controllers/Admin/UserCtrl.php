<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UserCtrl extends Controller
{

               //fetch all orders for admin
               public function all(Request $request)
               {
                   $user = User::latest()->get();
                   
           
                   if(!$user){
                       return response() ->json([
                           'status' =>false,
                           'message' => 'user could not be found',
                        
                       ]);
                   }
                   else{
                       return response() ->json([
                           'status' =>true,
                           'data' => [
                               'user' =>$user
                              
                           ],
                       ], 200);
                   }
               }
           
               public function recent(Request $request)
               {
                   $user = User::limit(3)->latest()->get();
                   
           
                   if(!$user){
                       return response() ->json([
                           'status' =>false,
                           'message' => 'user could not be found',
                        
                       ]);
                   }
                   else{
                       return response() ->json([
                           'status' =>true,
                           'data' => [
                               'user' =>$user
                              
                           ],
                       ], 200);
                   }
               }

               public function delete(Request $request, $id)
               {
                   $user =  User::findOrFail($id);
                   $user->delete();
           
                   if(!$user){
                       return response() ->json([
                           'status' =>false,
                           'data' => 'users could not be deleted'
                       ]);
                   }
           
           
                   return response() ->json([
                       'status' =>true,
                       'message' =>"user deleted successfully"
                   ], 200);
               }

               public function view(Request $request, $id)
               {
                   $user = User::findOrFail($id);
           
                   if(!$user){
                       return response() ->json([
                           'status' =>false,
                           'data' => 'users could not be found'
                       ]);
                   }
           
           
                   return response() ->json([
                       'status' =>true,
                       'data' => [
                           'user' =>$user
                       ],
                   ], 200);
               }
}