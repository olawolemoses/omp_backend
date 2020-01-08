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

class CategoriesCtrl extends Controller
{
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

}