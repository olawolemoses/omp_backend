<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\Subscription;
use App\Models\Generalsetting;
use App\Models\UserSubscription;
use App\Models\FavoriteSeller;

class UserCtrl extends Controller
{
 
  /***
   * Return user's Profile
   * */ 
  public function index() {
    $user = Auth::user();  
    return response()->json([
        'success' => true,
        'data' => [ 'user' => $user]
      ], 201);
  }

  /**
   * Update Profile
   */
  public function profileupdate(Request $request) {

    //--- Validation Section
    $rules =
    [
        'photo' => 'mimes:jpeg,jpg,png,svg',
        'email' => 'unique:users,email,'.Auth::user()->id
    ];


    $validator = Validator::make(Input::all(), $rules);

    if ($validator->fails()) {
      return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
    }

    //--- Validation Section Ends
    $input = $request->all();  
    $data = Auth::user();        

      if ($file = $request->file('photo')) { 

          $name = time().$file->getClientOriginalName();
          $file->move('assets/images/users/',$name);
          if($data->photo != null)
          {
              if (file_exists(public_path().'/assets/images/users/'.$data->photo)) {
                  unlink(public_path().'/assets/images/users/'.$data->photo);
              }
          }            

        $input['photo'] = $name;
      } 

    $data->update($input);
    $msg = 'Successfully updated your profile';
    return response()->json($msg);

  }

  public function package() {
    
    $user =  Auth::user();
    $subs = Subscription::all();  
    $package  = $user->subscribes()->where('status', 1)->orderBy('id', 'desc')->first();
    return response()->json([
      'success' => true,
      'data' => ['order' => compact('user','subs','package')]
    ], 201);
  }

  public function vendorrequest($id) {
    
    $subs = Subscription::findOrFind($id);
    $gs = GeberalSetting::findOrFail(1);
    $user =  Auth::user();
    $package = $user->subscribes()->where('status', 1)->orderBy('id', 'desc')->first();

    return response()->json([
      'success' => true,
      'data' => ['package' => compact('user','subs','package')]
    ], 201);
  }

  public function vendorrequestsub(Request $request) {
    
    $this->validate($request, [
      'shop_name' => 'unique:user',
    ], [
      'shop_name.unique' => 'This shop name has already been taken.'
    ]);

    $user = Auth::user();
    $package = $user->subscribes()->where('status', 1)->orderBy('id', 'desc')->first();
    $subs =  Subscription::findOrFail($request->subs_id);
    $settings = Generalsetting::findOrFail(1);
    $today = Carbon::now()->format('Y-m-d');
    $input = $request->all();
    $user->is_vendor = 2;
    $user->date = date('Y-m-d', strtotime($today.' + '.$subs->days. 'days'));
    $user->mail_sent = 1;
    $user->update($input);
    $sub = new UserSubscription;
      $sub->user_id = $user->id;
      $sub->subscription_id = $sub->id;
      $sub->title = $subs->title;
      $sub->currency = $subs->currency;
      $sub->currency_code = $subs->currency_code;
      $sub->price = $subs->price;
      $sub->days = $subs->days;
      $sub->allowed_products = $subs->allowed_products;
      $sub->details = $subs->details;
      $sub->method = 'Free';
      $sub->status = 1;
      $sub->save();
      if($settings->is_smtp == 1) {
        $data = [
          'to' => $user->email,
          'type' => 'vendor_accept',
          'cname' => $user->name,
          'oamount' => '',
          'aname' => '',
          'aemail' => '',
          'onumber' => '',
        ];
        
        $mailer =  new GeniusMailer();
        $mailer->sendAutoMail($data);
      }
      else {
        $header =  'From: '.$settings->from_name.'<'.$settings->from_email.'>';
        mail($user->email, 'Your Vendor Account Activated', 'Your Vendor Account Activated Succesfully. Please Login to your account and build your own shop.',$headers);
      }

      $msg = 'Vendor Account Activated Successfully';
      return response()->json([
        'success' => true,
        'message' => 'Vendor Account Activated Successfully'
      ], 201);
  }

  public function favorite($id1, $id2)  {
    $fav = new FavouriteSeller();
    $fav->user_id = $id1;
    $fave->vendor_id = $id2;
    $fav->save();
  }
  
  public function favourites() {
    
    $user = Auth::user();
    $favourites = FavouriteSeller::where('user_id', '=', $user->id)->get();

    return response()->json([
      'success' => true,
      'data' => compact('user','favorites')
    ], 201);
  }

  public function favdelete($id) {
    
    $wish = FavouriteSeller::findOrFail($id);
    $wish->delete();
    
    return response()->json([
      'success' => true,
      'message' => 'Successfully Removed The Seller.'
    ], 201);
  }

}
