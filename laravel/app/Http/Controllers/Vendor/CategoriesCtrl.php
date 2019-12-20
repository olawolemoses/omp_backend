<?php

namespace App\Http\Controllers\Vendor;

use App\Product;
use App\Order;
use App\Category;
use App\Subcategory;
use App\Childcategory;
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