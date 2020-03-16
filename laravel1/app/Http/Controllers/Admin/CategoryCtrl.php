<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use Cloudder;

class CategoryCtrl extends Controller
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
            'name' => 'required | string | unique:categories',
            'slug' => 'required | string',
            'photo' => 'mimes:jpeg,jpg,png,svg',
            
        ]);

        $category = new Category();
        $category ->name = $request->name;
        $category ->slug = $request->slug;
        $category ->status = 1;
        $category ->is_featured =0;

        // $photos = $request->file('photos');
        // $paths  = [];
    
        // foreach ($photos as $photo) {

        //     $cloudder = Cloudder::upload($photo->getRealPath());

        //         $uploadResult = $cloudder->getResult();
    
        //         $paths[]  = $uploadResult["url"];
        //         $category->photo = $paths ;
           
        // }
    
        // dd($paths);
        
        if($request->hasFile('photo') && $request->file('photo')->isValid()){
            $cloudder = Cloudder::upload($request->file('photo')->getRealPath());

            $uploadResult = $cloudder->getResult();

            $file_url = $uploadResult["url"];
            $category->photo = $file_url;
            
        }

        $category->save();

        if(!$category){
            return response() ->json([
                'status' =>false,
                'data' =>'Category could not be created'
            ]);
        }
        else{
            //return response if successful
        return response() ->json([
            'success' =>true,
            'data' => [
                'category' =>$category
            ],
        ], 201);
        }
        
    }

    //fetch all roles
    public function show(Request $request)
    {
        $category = Category::latest('id')->get();;

        if(!$category){
            return response() ->json([
                'status' =>false,
                'data' =>'Categories could not be found'
            ]);
        }
        else{
            //return response if successful
        return response() ->json([
            'status' =>true,
            'data' => [
                'category' =>$category
            ],
        ], 201);
        }

    }

    public function view(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        if(!$category){
            return response() ->json([
                'status' =>false,
                'data' =>'Categories could not be found'
            ]);
        }
        else{
            //return response if successful
        return response() ->json([
            'status' =>true,
            'data' => [
                'category' =>$category
            ],
        ], 201);
        }

    }

    //edit Category
    public function edit(Request $request, $id)
    {
        $category =  Category::findOrFail($id);
        
        if($category->fill($request->all())->save()) {

      
            return response([
                'status'=>true,
                'message'=>'Category updated successfully',
                'Category'=> $category
            ], 201);
        

        }
        return response()->json(['status' => 'failed to update Category']);
    }

    //delete Category
    public function delete(Request $request, $id)
    {
        $category =  Category::findOrFail($id);
        $category->delete();

        if(!$category){
            return response() ->json([
                'status' =>false,
                'data' =>'Category could not be deleted'
            ]);
        }
        else{
            //return response if successful
        return response() ->json([
            'status' =>true,
            'data' => [
                'category' =>$category
            ],
        ], 200);
        }

    }


}
