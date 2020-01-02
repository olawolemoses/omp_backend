<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;

class SubcategoryCtrl extends Controller
{
    //create middleware to check if request is being done by admin

    public function _construct()
    {
       
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
            'category_name' => 'required | string',
            'name' => 'required | string | unique:categories',
            'slug' => 'required | string',
            
            
        ]);

        $subcategory = new Subcategory();
        
        // $sub = Category::plunk($request->category_name);

        $subcategory ->name = $request->name;
        $subcategory ->slug = $request->slug;
        $subcategory ->status = 1;
        $subcategory ->category_name = $request->category_name;

        $subcategory->save();
        

        if(!$subcategory)
        {
            return response() ->json([
                'status' =>false,
                'data' => 'Subcategories could not be created'
            ], 403);
        }

        //return response if successful
        return response() ->json([
            'status' =>true,
            'data' => [
                'subcategory' =>$subcategory
            ],
        ], 201);
    }

    //fetch all roles
    public function show(Request $request)
    {
        $subcategory = Subcategory::latest('id')->get();

        if(!$subcategory)
        {
            return response() ->json([
                'status' =>false,
                'data' => 'Subcategories could not be found'
            ], 404);
        }

        return response() ->json([
            'status' =>true,
            'data' => [
                'subCategory' =>$subcategory
            ],
        ], 200);
    }

    public function view(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        if(!$subcategory)
        {
            return response() ->json([
                'status' =>false,
                'data' => 'Subcategories could not be found'
            ], 404);
        }


        return response() ->json([
            'status' =>true,
            'data' => [
                'subCategory' =>$subcategory
            ],
        ], 200);
    }

    //edit subCategory
    public function edit(Request $request, $id)
    {
        $subcategory =  Subcategory::findOrFail($id);
        
        if($subcategory->fill($request->all())->save()) {

      
            return response([
                'status'=>true,
                'message'=>'subCategory updated successfully',
                'subCategory'=> $subcategory
            ], 201);
        

        }
        return response()->json(['status'=>false,'message' => 'failed to update subCategory']);
    }

    //delete subCategory
    public function delete(Request $request, $id)
    {
        $subcategory =  Subcategory::findOrFail($id);
        $subcategory->delete();

        if(!$subcategory)
        {
            return response() ->json([
                'status' =>false,
                'data' => 'Subcategories could not be deleted'
            ], 401);
        }

        return response() ->json([
            'status' =>true,
            'message' =>"subCategory deleted successfully"
        ], 200);
    }


}
