<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use Cloudder;

class SubscriptionCtrl extends Controller
{


           //fetch all subscriptions for admin
    public function all(Request $request)
    {
        $subscription = Subscription::latest('id')->get();
        

        if(!$subscription){
            return response() ->json([
                'status' =>false,
                'message' => 'subscription could not be found',
             
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'subscription' =>$subscription
                   
                ],
            ], 200);
        }
    }


    public function view(Request $request, $id)
    {
        $subscription = subscription::findOrFail($id);

        if(!$subscription){
            return response() ->json([
                'status' =>false,
                'data' => 'subscriptions could not be found'
            ]);
        }


        return response() ->json([
            'status' =>true,
            'data' => [
                'subscription' =>$subscription
            ],
        ], 200);
    }


    public function delete(Request $request, $id)
    {
        $subscription =  subscription::findOrFail($id);
        $subscription->delete();

        if(!$subscription){
            return response() ->json([
                'status' =>false,
                'data' => 'subscriptions could not be deleted'
            ]);
        }


        return response() ->json([
            'status' =>true,
            'message' =>"subscription deleted successfully"
        ], 200);
    }

  }
