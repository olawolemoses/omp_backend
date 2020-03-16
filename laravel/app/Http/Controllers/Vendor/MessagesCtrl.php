<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Models\Message;
use App\Models\User;
use Auth;

class MessagesCtrl extends Controller
{
   
    //*** GET Request
    public function messageshow($id)
    {
        $conv = Message::findOrfail($id);
        if  (!$conv){
            return response() ->json([
                'status' =>false,
                'message' => 'Message could not be displayed'
            ]);
        }
        else{

            return response() ->json([
                'status' =>true,
                'data' =>$conv
            ], 200);
        }                
    }   

     //*** GET Request
     public function recievedmsg($recieved_user)
     {
         $conv = Message::where('recieved_user',$recieved_user)->latest()->get();
 
         if  (!$conv){
             return response() ->json([
                 'status' =>false,
                 'message' => 'Message could not be displayed'
             ]);
         }
         else{
 
             return response() ->json([
                 'status' =>true,
                 'data' =>$conv
             ], 200);
         }                
     }   

     //*** GET Request
     public function sentmessage($sent_user)
     {
         $conv = Message::where('sent_user',$sent_user)->latest()->get();
 
         if  (!$conv){
             return response() ->json([
                 'status' =>false,
                 'message' => 'Message could not be displayed'
             ]);
         }
         else{
 
             return response() ->json([
                 'status' =>true,
                 'data' =>$conv
             ], 200);
         }                
     }   

    //*** GET Request
    public function messagedelete($id)
    {
        $message =  Message::findOrFail($id);
        $message->delete();

        if(!$message){
            return response() ->json([
                'status' =>false,
                'message' => 'message could not be deleted'
            ]);
        }
        else{

            return response() ->json([
                'status' =>true,
                'message' =>"message deleted successfully"
            ], 200);
        };      
        //--- Redirect Section Ends               
    }

    //*** POST Request
    public function postmessage(Request $request)
    {
        $this->validate($request,[
            'conversation_id'=>'required | string',
            'message'=>'required | string',
            'subject'=>'required | string'
            
        ]);
        $msg = new Message();
        $input = $request->all();  
        $msg->fill($input)->save(); 
        //--- Redirect Section     
        if($msg){
            return response() ->json([
                'status' =>true,
                'message' => $msg
            ]);
        } 
        else{
            return response() ->json([
                'status' =>false,
                'message' => 'Message could not be posted'
            ]);
        }     
        //--- Redirect Section Ends    
    }

   
}