<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subcategory;
use App\Models\Childcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;

class ChildcategoryCtrl extends Controller
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
            'subcategory_name' => 'required | string',
            'category_name' => 'required | string',
            'name' => 'required | string | unique:categories',
            'slug' => 'required | string',
            
            
        ]);

        $childcategory = new Childcategory();
        
        // $sub = Subcategory::findOrFail($request->subcategory_name);

        $childcategory ->name = $request->name;
        $childcategory ->slug = $request->slug;
        $childcategory ->status = 1;
        $childcategory ->subcategory_name = $request->subcategory_name;
        $childcategory ->category_name = $request->category_name;


        $childcategory->save();

        if(!$childcategory){
            return response() ->json([
                'status' =>false,
                'data' =>'childCategory could not be created'
            ]);
        }
        else{
            //return response if successful
        return response() ->json([
            'status' =>true,
            'data' => [
                'childcategory' =>$childcategory
            ],
        ], 201);
        }
        
    }

    //fetch all roles
    public function show(Request $request)
    {
        $childcategory = Childcategory::latest('id')->get();

        if(!$childcategory){
            return response() ->json([
                'status' =>false,
                'data' =>'childCategory could not be found'
            ]);
        }
        else{
            //return response if successful
        return response() ->json([
            'status' =>true,
            'data' => [
                'childcategory' =>$childcategory
            ],
        ], 200);
        }

    }

    public function get(Request $request, $subcategory_name){
        $childcategory = Childcategory::where('subcategory_name', $subcategory_name)->get();

        if(!$childcategory)
        {
            return response() ->json([
                'status' =>false,
                'data' => 'Child category could not be found'
            ], 404);
        }


        return response() ->json([
            'status' =>true,
            'data' => [
                'childcategory' =>$childcategory
            ],
        ], 200);
    }

    public function view(Request $request, $id)
    {
        $childcategory = Childcategory::findOrFail($id);

        if(!$childcategory){
            return response() ->json([
                'status' =>false,
                'data' =>'childCategory could not be found'
            ]);
        }
        else{
            //return response if successful
        return response() ->json([
            'status' =>true,
            'data' => [
                'childcategory' =>$childcategory
            ],
        ], 200);
        }
    }

    //edit childCategory
    public function edit(Request $request, $id)
    {
        $childcategory =  Childcategory::findOrFail($id);
        
        if($childcategory->fill($request->all())->save()) {

      
            return response([
                'status'=>true,
                'message'=>'childCategory updated successfully',
                'childCategory'=> $childcategory
            ], 201);
        

        }
        return response()->json(['status' => 'failed to update childCategory']);
    }

    //delete childCategory
    public function delete(Request $request, $id)
    {
        $childcategory =  Childcategory::findOrFail($id);
        $childcategory->delete();

        if(!$childcategory){
            return response() ->json([
                'status' =>false,
                'data' =>'childCategory could not be deleted'
            ]);
        }
        else{
            //return response if successful
        return response() ->json([
            'status' =>true,    
            'message' =>"childCategory deleted successfully"
        ], 200);
        }

    }


}
