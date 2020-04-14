<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Hash;

use App\Models\Category;
use App\Models\Admin;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use Cloudder;

class ForgotPasswdCtrl extends Controller
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
            'email' => 'required | string | email'
        ]);

        $admin = Admin::whereEmail($request->email)->first();
        
        $email = $request->email;

        if($admin)
        {
            $expFormat = mktime(date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y"));
            
            $expDate = date("Y-m-d H:i:s",$expFormat);
            
            $key = md5(2418 * 2 . $email);
            
            $addKey = substr(md5(uniqid(rand(),1)),3,10);
            
            $key = $key . $addKey;
            
            $adminPasswordReset = new PasswordReset();
            
            $adminPasswordReset->email = $request->email;
            $adminPasswordReset->key = $key;
            $adminPasswordReset->expDate = $expDate;
            
            $adminPasswordReset -> save();
            
            
            $output='<p>Dear user,</p>';
            $output.='<p>Please click on the following link to reset your password.</p>';
            $output.='<p>-------------------------------------------------------------</p>';

            //$output.='<p><a href="http://localhost:4200/forgot-password/reset-password?key='.$key.'&email='.$email.'&action=reset" target="_blank"> http://localhost:4200/forgot-password/reset-password?key='.$key.'&email='.$email.'&action=reset</a></p>';		
            $output.='<p><a href="'. env('APP_URL') .'/admin/reset-password/'.$key.'/'.$email.'/reset" target="_blank">'. env('APP_URL') .'/admin/reset-password/'.$key.'/'.$email.'/reset</a></p>';		
            
            $output.='<p>-------------------------------------------------------------</p>';
            
            $output.='<p>Please be sure to copy the entire link into your browser.The link will expire after 1 day for security reason.</p>';
            $output.='<p>If you did not request this forgotten password email, no action is needed, your password will not be reset. However, you may want to log into your account and change your security password as someone may have guessed it.</p>';   	
            $output.='<p>Thanks,</p>';
            $output.='<p>All Things Africa</p>';
            
            $body = $output; 
            
            $subject = "Password Recovery - AllThingsAfrica.co";
            
            $email_to = $email;
            $fromserver = "no-reply@allthingsafrica.co";
            
            $email = new \SendGrid\Mail\Mail(); 
            $email->setFrom($fromserver, "Admin AllThingsAfrica");
            $email->setSubject($subject);
            $email->addTo($email_to, "Admin User");
            $email->addContent(
                "text/html", $output
            );
            
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
            try {
                $response = $sendgrid->send($email);
                // print $response->statusCode() . "\n";
                // print_r($response->headers());
                // print $response->body() . "\n";
                
                return response()->json([
                    'status' => true,
                    'message' => 'Password reset email sent. ',
                    'data' => $response->body()
                ]);
                
            } catch (Exception $e) {
                return response()->json([
                    'status' => true,
                    'message' => 'Password reset email sent. ',
                    'data' => $e->getMessage()
                ], 422);                
            }            
            
            
        }  
        else
        {
            return response()->json([
                'status' => false,
                'data' => 'Email could not be sent to this email address.'
            ]);
        }
    }

    //fetch all roles
    public function reset(Request $request)
    {
        $this->validate($request,[
            'newPassword' => 'required | string',
            'confirmPassword' => 'required | string',
            'email' => 'required | string | email',
            'action' => 'required | string',
            'key' => 'required | string',
        ]);
        
        
        $key = $request->key;
        $email = $request->email;
        $action = $request->action;
        $newPassword = $request->newPassword;
        $confirmPassword = $request->confirmPassword;
        
        if(isset($key) && isset($email) && isset($action) && $action === "reset"){
            
            $adminPasswordReset = PasswordReset::where("key", $key)
                                                ->where("email", $email)
                                                ->first();
             
            if($adminPasswordReset) {
                
                $curDate = date("Y-m-d H:i:s");
                
                $expDate = $adminPasswordReset->expDate; 
                
                // check if the expiry date has reached
                if ($expDate <= $curDate){
                 
                    return response()->json([
                        'status' => false,
                        'data' => 'Password reset link has expired!',
                    ], 422);
                
                }
                
                // confirm passwords again
                if($newPassword != $confirmPassword){
                    return response()->json([
                        'status' => false,
                        'data' => 'Passwords not confirmed!',
                    ], 422);
                }
                
                $admin = Admin::whereEmail($email)->first();
                $admin->password = Hash::make($request->newPassword);
                $admin->save();
                
                // delete rows from password reset table
                
                $deletedRows = PasswordReset::where('email', $email)->delete();
                
                return response()->json([
                    'status' => true,
                    'data' => 'Password reset successful!',
                ], 201);
            
            } else {
                // confirm passwords again
                return response()->json([
                    'status' => false,
                    'data' => 'Password reset failed!',
                ], 422);     
            }
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
