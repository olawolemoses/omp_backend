<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use Cloudder;


class ProductCtrl extends Controller
{

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
            'product_type' => 'required | string',
            'user_id' =>'required | numeric',
            'category_name' => 'required | string',   
            'name' => 'required | string',
            // 'photo' => 'mimes:jpeg,jpg,png,svg',
            'price' => 'required',
            'details' => 'required | string',
            'policy' => 'required | string',
            'views' => 'numeric',
            'tags' => 'required | string',
            'product_condition' => 'required | string',
            'type' => 'required | string',
            
        ]);

        $product = new Product();

        $product ->sku = $request->sku;
        $product ->product_type = $request->product_type;
        $product ->affiliate_link = $request->affiliate_link;
        $product ->user_id = $request->user_id;
        $product ->category_name = $request->category_name;
        $product ->category_id = $request->category_id;
        $product ->subcategory_id = $request->subcategory_id;
        

        $product ->subcategory_name = $request->subcategory_name;
        $product ->childcategory_name = $request->childcategory_name; 
        $product ->name = $request->name;
        $product ->slug = $request->slug;

        $photos = $request->file('photo');
        $paths  = [];
    
        foreach ($photos as $photo) {

            $cloudder = Cloudder::upload($photo->getRealPath());

                $uploadResult = $cloudder->getResult();
    
                $paths[]  = $uploadResult["url"];
                
                $product->photo = $paths;
           
        }
    
        if($request->hasFile('file') && $request->file('file')->isValid()){
            $cloudder = Cloudder::upload($request->file('file')->getRealPath());

            $uploadResult = $cloudder->getResult();

            $file_url = $uploadResult["url"];
            $product->file = $file_url;
            
        }

        $product ->size = $request->size;
        $product ->thumbnail = $uploadResult["url"];
        $product ->size_qty = $request->size_qty;
        $product ->size_price = $request->size_price;
        $product ->price = $request->price;
        $product ->previous_price = $request->previous_price;
        $product ->details = $request->details;
        $product ->stock = $request->stock;
        $product ->policy = $request->policy;
        $product ->status = 1;
        $product ->views = 0;
        $product ->tags = $request->tags;
        $product ->features = $request->features;
        $product ->colors = $request->colors;
        $product ->product_condition = $request->product_condition;
        $product ->ship = $request->ship;
        $product ->is_meta = 0;
        $product ->meta_tag = $request->meta_tag;
        $product ->meta_description = $request->meta_description;
        $product ->youtube = $request->youtube;
        $product ->type = $request->type;
        $product ->license = $request->license;
        $product ->license_qty = $request->license_qty;
        $product ->link = $request->link;
        $product ->platform = $request->platform;
        $product ->region = $request->region;
        $product ->licence_type = $request->licence_type;
        $product ->measure = $request->measure;
        $product ->featured = 0;
        $product ->best = 0;
        $product ->top = 0;
        $product ->hot = 0;
        $product ->latest = 0;
        $product ->big = 0;
        $product ->trending = 0;
        $product ->sale = 0;
        $product ->is_discount = 0;
        $product ->discount_date = $request->discount_date;
        $product ->whole_sell_qty = $request->whole_sell_qty;
        $product ->whole_sell_discount = $request->whole_sell_discount;
        $product ->packaging_price = $request->packaging_price;

        $product->save();

        if(!$product){
                return response() ->json([
                    'status' =>false,
                    'message' => 'product could not be created'
                ]);
            }
        else{
            //return response if successful
            return response() ->json([
                'status' =>true,
                'data' => [
                    'product' =>$product
                ],
            ], 201);
        }
        
    }

    //fetch all product
    public function show(Request $request)
    {
        $product = Product::latest()->get();
        

        if(!$product){
            return response() ->json([
                'status' =>false,
                'message' => 'product could not be found',
             
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'product' =>$product
                   
                ],
            ], 200);
        }
    }

    //fetch all product
    public function prodid(Request $request, $user_id)
    {
        $product = Product::where('user_id', $user_id)->where('status','=',1)->latest()->get();

        if(!$product){
            return response() ->json([
                'status' =>false,
                'message' => 'product could not be found'
        
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'product' =>$product
                   
                ],
            ], 200);
        }
    }

    public function view(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if  (!$product){
            return response() ->json([
                'status' =>false,
                'message' => 'product could not be displayed'
            ]);
        }
        else{

            return response() ->json([
                'status' =>true,
                'data' => [
                    'product' =>$product
                ],
            ], 200);
        }
    }

    //edit product
    public function edit(Request $request, $id)
    {
        $product =  Product::findOrFail($id);
        
        if($product->fill($request->all())->save()) {

      
            return response([
                'status'=>true,
                'message'=>'product updated successfully',
                'product'=> $product
            ], 201);
        

        }
        return response()->json(['status'=>false,'message' => 'failed to update product']);
    }

    //delete product
    public function delete(Request $request, $id)
    {
        $product =  Product::findOrFail($id);
        $product->delete();

        if(!$product){
            return response() ->json([  
                'status' =>false,
                'message' => 'product could not be deleted'
            ]);
        }
        else{

            return response() ->json([
                'status' =>true,
                'message' =>"product deleted successfully"
            ], 200);
        }
    }

    public function recentorder(Request $request)
    {
        $product = Product::limit(5)->latest()->get();

        if(!$product){
            return response() ->json([
                'status' =>false,
                'message' => 'product could not be found'
        
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'product' =>$product
                   
                ],
            ], 200);
        }
    
    }

    public function deactivated (Request $request,$id){
        $product = Product::findOrFail($id);
            if($product)
            {
                //set status to 0
                $product->status= 0;
                $product->update();
                return response()->json([
                    'status'=>true,
                    'msg'=>'Product deactivated' 
                ],200);
            }
            else{
                return response()->json([
                    'status'=>false,
                    'msg'=>'error deactivating product'
                ]);
            }

    }


    public function viewdeactivated (Request $request,$user_id){
        $product = Product::where('user_id',$user_id)->where('status','=',0)->latest()->get();
            if($product){
                return response()->json([
                    'status'=>true,
                    'msg'=>'deactivated products fetched successfully',
                    'data' => [
                        'product' =>$product
                    ],
                ]);
            }
            else{
                return response()->json([
                    'status'=>false,
                    'msg'=>'failed to fetch product'
                ]);
            }

    }


    public function activated (Request $request,$id){
        $product = Product::findOrFail($id);
            if($product)
            {
                //set status to 0
                $product->status = 1;
                $product->update();
                return response()->json([
                    'status'=>true,
                    'msg'=>'Product activated' 
                ],200);
            }
            else{
                return response()->json([
                    'status'=>false,
                    'msg'=>'error deactivating product'
                ]);
            }

    }

   

}