<?php

namespace App\Http\Controllers\Front;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class SearchCtrl extends Controller
{

    public function search(Request $request, $name){
        $product = Product::where('name', $name)->get();

        if(!$product)
        {
            return response() ->json([
                'status' =>false,
                'data' => 'Product could not be found'
            ], 404);
        }


        return response() ->json([
            'status' =>true,
            'data' => [
                'product' =>$product
            ],
        ], 200);
    }

}