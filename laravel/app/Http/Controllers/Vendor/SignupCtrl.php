<?php
  
  namespace App\Http\Controllers\Vendor;

  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Auth;
  use App\Models\User;
  use Validator;
  use Cloudder;
 
  use JWTAuth;
  use Log;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Hash;
use Illuminate\Auth\Events\PasswordReset;

  class SignupCtrl extends controller{
      
       use SendsPasswordResetEmails, ResetsPasswords {
        SendsPasswordResetEmails::broker insteadof ResetsPasswords;
    }
    
    public function _construct(Request $request) {
        $this->request = $request;
    }
    
    public function callResetPassword(Request $request)
    {
        return $this->reset($request);
    }

    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
        event(new PasswordReset($user));
    }

    public function sendPasswordResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }
    
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json([
            'message' => 'Password reset email sent.',
            'data' => $response
        ]);
    }    
    
    
    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Password reset successfully.']);
    }
    
    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Failed, Invalid Token.']);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Email could not be sent to this email address.']);
    }    


    public function create (Request $request){
        $this->validate($request,[
            'name' => 'required | string',
            'email' => 'required | string :unique',
            'password' => 'required | string',   
            'address' => 'required | string',
           
            'zip' => 'required',
            'country' => 'required | string',
            'state' => 'required | string',
            'shop_name' => 'required | string',
            'shop_details' => 'required | string',
            
          
            
        ]);

        $vendor = new User();

       
        $vendor ->address = $request->address;
        $vendor ->shop_number = $request->shop_number;
        $vendor ->email_verified = 'No';
        // $cat = Category::findOrFail($request->category_id);
        $vendor ->password = Hash::make($request->password);
        $vendor ->is_vendor = 1;
        $vendor ->verification_link = $request->verification_link; 
        $vendor ->name = $request->name;
        $vendor ->fax = $request->fax;
        $vendor ->phone = $request->phone;
        
        if($request->hasFile('photo') && $request->file('photo')->isValid()){
            $cloudder = Cloudder::upload($request->file('photo')->getRealPath());

            $uploadResult = $cloudder->getResult();

            $file_url = $uploadResult["url"];
            $vendor->photo = $file_url;
            
        }

        
        if($request->hasFile('shop_image') && $request->file('shop_image')->isValid()){
            $cloudder = Cloudder::upload($request->file('shop_image')->getRealPath());

            $uploadResult = $cloudder->getResult();

            $file_url = $uploadResult["url"];
            $vendor->shop_image = $file_url;
            
        }

        if($request->hasFile('bill') && $request->file('bill')->isValid()){
            $cloudder = Cloudder::upload($request->file('bill')->getRealPath());

            $uploadResult = $cloudder->getResult();

            $file_url = $uploadResult["url"];
            $vendor->bill = $file_url;
            
        }

       if($request->hasFile('id_card') && $request->file('id_card')->isValid()){
            $cloudder = Cloudder::upload($request->file('id_card')->getRealPath());

            $uploadResult = $cloudder->getResult();

            $file_url = $uploadResult["url"];
            $vendor->id_card = $file_url;
            
        }

        $vendor ->email = $request->email;
        $vendor ->affilate_code = $request->affilate_code;
        $vendor ->zip = $request->zip;
        $vendor ->affilate_income = $request->affilate_income;
        $vendor ->shop_name = $request->shop_name;
        $vendor ->country = $request->country;
        $vendor ->shop_address = $request->address;
        $vendor ->state = $request->state;
        $vendor ->city = $request->city;
        $vendor ->remember_token = $request->remember_token;
        $vendor ->status = 0;
        $vendor ->is_provider = 0;
        $vendor ->shop_details = $request->shop_details;
        $vendor ->owner_name = $request->owner_name;
        $vendor ->reg_number = $request->reg_number;
        $vendor ->shop_message = $request->shop_message;
        $vendor ->f_url = 0;
        $vendor ->g_url =0;
        $vendor ->l_url = 0;
        $vendor ->t_url = 0;
        $vendor ->f_check = 0;
        $vendor ->g_check = 0;
        $vendor ->t_check = 0;
        $vendor ->l_check = 0;
        $vendor ->mail_sent = 0;
        $vendor ->shipping_cost = 0;
        $vendor ->current_balance = 0;
        $vendor ->date = $request->date;
        $vendor ->ban = 0;

        $vendor->save();

        if(!$vendor){
                return response() ->json([
                    'status' =>false,
                    'message' => 'vendor could not be created'
                ]);
            }
        else{
            //return response if successful
            return response() ->json([
                'status' =>true,
                'data' => [
                    'vendor' =>$vendor
                ],
            ], 201);
        }
    }


     public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::whereEmail($request->email)->first();

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => false,'error' => 'invalid_credentials'], 200);
            }
            else{
                // $ven = $user->id::where('is_vendor','=',1);
                $ven = $user->id;
                $gs = User::find($ven);
                $gs1 =$user->password;

                
                if($gs->is_vendor == 1){
                    return response()->json([
                        'status' => true,
                        'data' => [ 'user' => $gs],

                        'token' => JWTAuth::fromUser($user),
                    // 'token' => $this->getAuthTokenData($user),
                ], 201); 

                }


                else{
                    return response()->json(['status' => false,'error' => 'User not registered as vendor'], 200);
                    
                }
            
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function show (Request $request, $id){
        $vendor = User::findOrFail($id);
        if(!$vendor){
            return response() ->json([
                'status' =>false,
                'data' => 'vendor could not be found'
            ]);
        }


        return response() ->json([
            'status' =>true,
            'data' => [
            'vendor' =>$vendor
            ],
        ], 200);
    }

    public function checkpassword (Request $request, $id){
        $vendor = User::findOrFail($id);

        $hasher = app()->make('hash');
        if(!$vendor){
            return response() ->json([
                'status' =>false,
                'data' => 'vendor could not be found'
            ]);
        }
        else{
           
            $gs = $hasher->check($request->input('password'),$vendor->password);
            
            if($gs){
             
                    return response() ->json([
                        'status' =>true,
                        'message' =>'Password exists',
                    ], 200);
    
            }
            else
                {
                    return response()->json([
                        'status' =>false,
                        'message' =>'Incorrect password',
                    ],200);
            
                }
       
        }

                
        }
    

 
    
    
    public function delete(Request $request, $id)
        {
            $user =  User::findOrFail($id);
            $user->delete();
    
            if(!$user){
                return response() ->json([
                    'status' =>false,
                    'data' => 'users could not be deleted'
                ]);
            }
    
    
            return response() ->json([
                'status' =>true,
                'message' =>"user deleted successfully"
            ], 200);
        

       
    }

    public function all(Request $request)
    {   
        $user = User::where('is_vendor','=','1')->latest()->get();
        

        if(!$user){
            return response() ->json([
                'status' =>false,
                'message' => 'user could not be found',
             
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'user' =>$user
                   
                ],
            ], 200);
        }
    }

    public function password (Request $request, $id){
       
        $vendor = User::findOrFail($id);

       
        $vendor->password = Hash::make($request->password);

        $vendor->update();

        if($vendor){
            return response() ->json([
                'status' =>'success',
                'message' =>'Password updated successfully'
            ], 200);  
        }

        return response() ->json([
            'status' =>false,
            'message' =>'Failed to update password'
        ], 200);

    }  


     public function updated (Request $request,$id){
       
        $vendor = User::findOrFail($id);

        if($request->hasFile('photo') && $request->file('photo')->isValid()){
            $cloudder = Cloudder::upload($request->file('photo')->getRealPath());

            $uploadResult = $cloudder->getResult();

            $file_url = $uploadResult["url"];
            $vendor->photo = $file_url;
            
        }
        // else {
        //        # code...
        //        return response()->json([
        //          'status' => 'Failed',
        //          'message' => 'Select image first.'
        //        ], 201);
        //      }

        $vendor->phone = $request->phone;
        $vendor->name = $request->name;
        // $vendor->password = Hash::make($request->password);
        $vendor->email = $request->email;
        $vendor->address = $request->address;
        $vendor->zip = $request->zip;
        $vendor->city = $request->city;
        $vendor->state = $request->state;
        $vendor->shop_name = $request->shop_name;
        $vendor->shop_details = $request->shop_details;
        $vendor->current_balance = $request->current_balance;


        $vendor->save();

        if($vendor){
            return response() ->json([
                'status' =>'success',
                'message' =>'Profile updated successfully'
            ], 200);  
        }

        return response() ->json([
            'status' =>false,
            'message' =>'Failed to update profile'
        ], 200);

    }  
    
     public function checkemail(request $request){
      
        $ven = User::whereEmail($request->email)->first();

        if($ven){
            return response()->json([
                'status' => true,
                'message' => 'Email exist'
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Email does not exist'
            ]);
        }

    }
   

  }
