<?php

  namespace App\Http\Controllers\User;

  use Illuminate\Http\Request;
  use App\Http\Controllers\Controller;
  use Auth;
  use App\Models\Currency;
  use App\Models\Generalsetting;
  use App\Models\User;
  use App\Models\Withdraw;
  use Illuminate\Support\Facades\Input;
  use Validator;

  class WithdrawCtrl  extends Controller 
  {
    public function index() {
      $user = Auth::user();
      $withdraws = Withdraw::where('user_id', '=', $user->id)
                    ->where('type', '=', 'user')
                    ->orderBy('id', 'DESC')->get();
      $sign = Currency::where('is_default', '=', 1)->first();
      
      return response()->json([
          'success' => true,
          'data' => compact('withdraws','sign')
        ], 201);
    }

    public function affilate_code() {
      
      $user = Auth::user();

      return response()->json([
          'success' => true,
          'data' => compact('user')
        ], 201);
    }

    public function create(Request $request) {
      
      $from = User::findOrFail(Auth::user()->id);

      $withdrawcharge = Generalsetting::findOrFail(1);
      $charge = $withdrawcharge->withdraw_fee;

      if ($request->amount > 0) {

        $amount = $request->amount; 

        if ($from->affliate_income >= $amount) {
          $fee = (($withdrawcharge->withdraw_charge / 100) * $amount) + $charge;
          $finalamount = $amount - $fee;
          if ($from->affliate_income >= $finalamount) {
            $finalamount = number_format((float) $finalamount, 2, '.', '');

            $from->affliate_income = $from->affliate_income - $amount;
            $from->update();

            $newwithdraw = new Withdraw();
            $newwithdraw['user_id'] = Auth::user()->id;
            $newwithdraw['method'] = $request->methods;
            $newwithdraw['acc_email'] = $request->acc_email;
            $newwithdraw['iban'] = $request->iban;
            $newwithdraw['country'] = $request->country;
            $newwithdraw['acc_name'] = $request->acc_name;
            $newwithdraw['address'] = $request->address;
            $newwithdraw['swift'] = $request->swift;
            $newwithdraw['reference'] = $request->reference;
            $newwithdraw['amount'] = $finalamount;
            $newwithdraw['fee'] = $fee;
            $newwithdraw['type'] = 'user';

            $newwithdraw->save();

            return response()->json('Withdraw Request Sent Successfully.');
          } else {
            return response()->json(array('errors' => [0 => 'Insufficient Balance.']));
          }
        } else {
          return response()->json(array('errors' => [0 => 'Insufficient Balance.']));
        }
      }

      return response()->json(array('errors' => [0 => 'Please enter a valid amount.']));
    }

  }
  