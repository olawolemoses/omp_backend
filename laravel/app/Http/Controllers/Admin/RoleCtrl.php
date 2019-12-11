<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;

class RoleCtrl extends Controller
{
    //create middleware to check if request is being done by admin

    public function _construct()
    {
        $this->middleware('auth:admin');
    }

     /**
     * Creates and authenticates a new user
     *
     * @param Request $request
     * @return JsonResponse
     * @throws HttpException
     * @throws ValidationException
     */

    public function create(Request $request)
    {
        $this->validate($request,[
            'name' => 'required | string | unique:roles',
            'permission' => 'required | string'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'permission' => $request->permission
        ]);

        if(!$role){
            return response() ->json([
                'status' =>false,
                'data' => 'Roles could not be created'
            ]);
        }

        //return response if successful
        return response() ->json([
            'status' =>true,
            'data' => [
                'role' =>$role
            ],
        ], 201);
    }

    //fetch all roles
    public function show(Request $request)
    {
        $role = Role::latest('id')->get();;

        if(!$role){
            return response() ->json([
                'status' =>false,
                'data' => 'Roles could not be found'
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'role' =>$role
                ],
            ], 200);
        }
       
    }

    public function view(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if(!$role){
            return response() ->json([
                'status' =>false,
                'data' => 'Roles could not be found'
            ]);
        }


        return response() ->json([
            'status' =>true,
            'data' => [
                'role' =>$role
            ],
        ], 200);
    }

    //edit role
    public function edit(Request $request, $id)
    {
        $role =  Role::findOrFail($id);
        
        if($role->fill($request->all())->save()) {

      
            return response([
                'status'=>true,
                'message'=>'User updated successfully',
                'role'=> $role
            ], 201);
        

        }
        return response()->json(['status'=>false,'message' => 'failed to update role']);
    }

    //delete role
    public function delete(Request $request, $id)
    {
        $role =  Role::findOrFail($id);
        $role->delete();

        if(!$role){
            return response() ->json([
                'status' =>false,
                'data' => 'Roles could not be deleted'
            ]);
        }


        return response() ->json([
            'status' =>true,
            'message' =>"Role deleted successfully"
        ], 200);
    }


}
