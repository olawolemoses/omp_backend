<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use App\Order;
use App\Category;
use App\Subcategory;
use App\Childcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use Cloudder;

class OrderCtrls extends Controller
{
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

    public function index(Request $request){
        $orders = Order::where('status', '=', 'pending')->orderBy('status', 'desc')->get()->groupBy('status');

          return response()->json([
            'status' => true,
            'data' => $orders
          ], 201);
    }
    

    //fetch all orders
    public function process(Request $request)
    { 
        
        $orders = Order::where('status', '=', 'processing')->orderBy('status', 'desc')->get()->groupBy('status');

        return response()->json([
          'status' => true,
          'data' => $orders
        ], 201);
    }

    public function complete() {
         
        $orders = Order::where('status', '=', 'completed')->orderBy('status', 'desc')->get()->groupBy('status');

        return response()->json([
          'status' => true,
          'data' => $orders
        ], 201);
    }

    public function decline() {
         
        $orders = Order::where('status', '=', 'declined')->orderBy('status', 'desc')->get()->groupBy('status');

        return response()->json([
          'status' => true,
          'data' => $orders
        ], 201);
    }

           //fetch all orders for admin
    public function all(Request $request)
    {
        $order = Order::latest()->get();
        

        if(!$order){
            return response() ->json([
                'status' =>false,
                'message' => 'order could not be found',
             
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'order' =>$order
                   
                ],
            ], 200);
        }
    }

    public function recent(Request $request)
    {
        $order = Order::latest()->get();
        

        if(!$order){
            return response() ->json([
                'status' =>false,
                'message' => 'order could not be found',
             
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'order' =>$order
                   
                ],
            ], 200);
        }
    }

    public function view(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if(!$order){
            return response() ->json([
                'status' =>false,
                'data' => 'orders could not be found'
            ]);
        }


        return response() ->json([
            'status' =>true,
            'data' => [
                'order' =>$order
            ],
        ], 200);
    }


    public function delete(Request $request, $id)
    {
        $order =  Order::findOrFail($id);
        $order->delete();

        if(!$order){
            return response() ->json([
                'status' =>false,
                'data' => 'orders could not be deleted'
            ]);
        }


        return response() ->json([
            'status' =>true,
            'message' =>"order deleted successfully"
        ], 200);
    }

  }
