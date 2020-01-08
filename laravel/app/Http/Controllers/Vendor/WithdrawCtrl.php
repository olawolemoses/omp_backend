<?php

  namespace App\Http\Controllers\Vendor;

  use App\Models\User;
  use App\Models\Withdraw;
  use App\Models\Generalsetting;
  use Auth;
  use App\Models\Currency;
  use Illuminate\Http\Request;
  use App\Http\Controllers\Controller;

  class WithdrawCtrl extends Controller
  {
    public function index() {      
      $withdraws = Withdraw::where('user_id', '=', Auth::user()->id)
              ->where('type', '=', 'vendor')->orderBy('id', 'desc')->get();
      $sign = Currency::where('is_default')->first();
      return response()->json([
        'success' => true,
        'data' => compact('withdraws','sign')
      ], 201);
    }

    public function create(Request $request) {

      $from = User::findOrFail(Auth::user()->id);

      $withdrawcharge = Generalsetting::findOrFail(1);
      $charge = $withdrawcharge->withdraw_fee;

      if ($request->amount > 0) {
        
        $amount = $request->amount;
        
        if ($from->current_balance >= $amount) {
          $fee = (($withdrawcharge->withdraw_charge / 100) * $amount) + $charge;
          $finalamount = $amount - $fee;
          $finalamount = number_format((float)$finalamount,2,'.','');

          $from->current_balance = $from->current_balance - $amount;
          $from->update();

          $newwithdraw = new Withdraw();
          $newwithdraw['user_id'] = Auth::user()->id;
          $newwithdraw['method'] = $request->methods;
          $newwithdraw['acc_email'] = $request->acc_email;
          $newwithdraw['iban'] = $request->iban;
          $newwithdraw['country'] = $request->acc_country;
          $newwithdraw['acc_name'] = $request->acc_name;
          $newwithdraw['address'] = $request->address;
          $newwithdraw['swift'] = $request->swift;
          $newwithdraw['reference'] = $request->reference;
          $newwithdraw['amount'] = $finalamount;
          $newwithdraw['fee'] = $fee;
          $newwithdraw['type'] = 'vendor';
          $newwithdraw->save();

          return response()->json('Withdraw Request Sent Successfully.'); 

        } else {
          return response()->json(array('errors' => [ 0 => 'Insufficient Balance.' ])); 
        }
      }

      return response()->json(array('errors' => [ 0 => 'Please enter a valid amount.' ])); 
    }

    

  }
  