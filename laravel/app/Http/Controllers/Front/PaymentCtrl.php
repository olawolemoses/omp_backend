<?php

  namespace App\Http\Controllers\Front;

  use App\Classes\GeniusMailer;
  use App\Http\Controllers\Controller;
  use App\Cart;
  use App\Coupon;
  use App\Currency;
  use App\Generalsetting;
  use App\Notification;
  use App\Order;
  use App\OrderTrack;
  use App\Product;
  use App\User;
  use App\UserNotification;
  use App\VendorOrder;
  use Config;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Session;
  use Srmklive\PayPal\Services\ExpressCheckout;


  class PaymentCtrl extends Controller
  {

    public function __construct() {
      //Set Spripe Keys
      $gs = Generalsetting::findOrFail(1);
      Config::set('paypal.mode', $gs->paypal_mode);
      Config::set('paypal.'.$gs->paypal_mode.'.username', $gs->paypal_username);
      Config::set('paypal.'.$gs->paypal_mode.'.password', $gs->paypal_password);
      Config::set('paypal.'.$gs->paypal_mode.'.secret', $gs->paypal_secret);

      if (Session::has('currency')) {
        $this->curr = Currency::find(Session::get('currency'));
      }
      else {
        $this->curr = Currency::where('is_default','=',1)->first();
      }

      Config::set('paypal.currency', $this->curr->name);
    }


    public function store(Request $request) {

        $input = $request->all();
        Session::put('paypal_data',$input);

        if($request->pass_check) {
          $users = User::where('email','=',$request->personal_email)->get();
          if(count($users) == 0) {
            if ($request->personal_pass == $request->personal_confirm){
                $user = new User;
                $user->name = $request->personal_name; 
                $user->email = $request->personal_email;   
                $user->password = bcrypt($request->personal_pass);
                $token = md5(time().$request->personal_name.$request->personal_email);
                $user->verification_link = $token;
                $user->affilate_code = md5($request->name.$request->email);
                $user->save();
                Auth::guard('web')->login($user);                     
            } else {
                return redirect()->back()->with('unsuccess',"Confirm Password Doesn't Match.");     
            }
          }
          else {
              return redirect()->back()->with('unsuccess',"This Email Already Exist.");  
          }
        }

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
        }
        
        $settings = Generalsetting::findOrFail(1);
        $provider = new ExpressCheckout;
        $data['items'][] = [
          'name' => $settings->title." Order",
          'price' => $request->total + $request->shipping_cost + $request->packing_cost
        ];
        $data['invoice_description'] = '';
        $data['invoice_id'] = str_random(4).time();
        $data['total'] = $request->total + $request->shipping_cost + $request->packing_cost;
        $data['return_url'] = action('Front\PaymentController@notify');
        $data['cancel_url'] = action('Front\PaymentController@paycancle');
        $response = $provider->setExpressCheckout($data);
        Session::put('paypal_items',$data);
        return redirect($response['paypal_link']);
    }

    public function notify(Request $request){

            $paypal_data = Session::get('paypal_data');
            $paypal_items = Session::get('paypal_items');
            $success_url = action('Front\PaymentController@payreturn');
            $cancel_url = action('Front\PaymentController@paycancle');
            $provider = new ExpressCheckout;
            $token = $request->token;
            $payerid = $request->PayerID;
            $response = $provider->getExpressCheckoutDetails($token);
            $item_number = $response['INVNUM'];
            $response = $provider->doExpressCheckoutPayment($paypal_items,$token,$payerid);
            $item_amount = $response['PAYMENTINFO_0_AMT'];
            // dd($response);
            if($response['PAYMENTINFO_0_ACK'] == 'Success'){

              $oldCart = Session::get('cart');
                $cart = new Cart($oldCart);

                foreach($cart->items as $key => $prod)
                {
                    if(!empty($prod['item']['license']) && !empty($prod['item']['license_qty']))
                    {
                            foreach($prod['item']['license_qty']as $ttl => $dtl)
                            {
                                if($dtl != 0)
                                {
                                    $dtl--;
                                    $produc = Product::findOrFail($prod['item']['id']);
                                    $temp = $produc->license_qty;
                                    $temp[$ttl] = $dtl;
                                    $final = implode(',', $temp);
                                    $produc->license_qty = $final;
                                    $produc->update();
                                    $temp =  $produc->license;
                                    $license = $temp[$ttl];
                                    $oldCart = Session::has('cart') ? Session::get('cart') : null;
                                    $cart = new Cart($oldCart);
                                    $cart->updateLicense($prod['item']['id'],$license);  
                                    Session::put('cart',$cart);
                                    break;
                                }                    
                            }
                    }
                }

                        $settings = Generalsetting::findOrFail(1);
                        $order = new Order;

                        $order['user_id'] = $paypal_data['user_id'];
                        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
                        $order['total_quantity'] = $paypal_data['total_quantity'];
                        $order['pay_amount'] = $item_amount;
                        $order['method'] = $paypal_data['method'];
                        $order['customer_email'] = $paypal_data['email'];
                        $order['customer_name'] = $paypal_data['name'];
                        $order['customer_phone'] = $paypal_data['phone'];
                        $order['order_number'] = $item_number;
                        $order['shipping'] = $paypal_data['shipping'];
                        $order['pickup_location'] = $paypal_data['pickup_location'];
                        $order['customer_address'] = $paypal_data['address'];
                        $order['customer_country'] = $paypal_data['customer_country'];
                        $order['customer_city'] = $paypal_data['city'];
                        $order['customer_zip'] = $paypal_data['zip'];
                        $order['shipping_email'] = $paypal_data['shipping_email'];
                        $order['shipping_name'] = $paypal_data['shipping_name'];
                        $order['shipping_phone'] = $paypal_data['shipping_phone'];
                        $order['shipping_address'] = $paypal_data['shipping_address'];
                        $order['shipping_country'] = $paypal_data['shipping_country'];
                        $order['shipping_city'] = $paypal_data['shipping_city'];
                        $order['shipping_zip'] = $paypal_data['shipping_zip'];
                        $order['order_note'] = $paypal_data['order_notes'];
                        $order['coupon_code'] = $paypal_data['coupon_code'];
                        $order['coupon_discount'] = $paypal_data['coupon_discount'];
                        $order['payment_status'] = $response['PAYMENTINFO_0_PAYMENTSTATUS'];
                        $order['currency_sign'] = $this->curr->sign;
                        $order['currency_value'] = $this->curr->value;
                        $order['shipping_cost'] = $paypal_data['shipping_cost'];
                        $order['packing_cost'] = $paypal_data['packing_cost'];
                        $order['tax'] = $paypal_data['tax'];
                        $order['dp'] = $paypal_data['dp'];
                        $order['txnid'] = $response['PAYMENTINFO_0_TRANSACTIONID'];

                        $order['vendor_shipping_id'] = $paypal_data['vendor_shipping_id'];
                        $order['vendor_packing_id'] = $paypal_data['vendor_packing_id'];

                        if($order['dp'] == 1) {
                            $order['status'] = 'completed';
                        }

                        if (Session::has('affilate')) {
                            $val = $item_amount / 100;
                            $sub = $val * $settings->affilate_charge;
                            $user = User::findOrFail(Session::get('affilate'));
                            $user->affilate_income += $sub;
                            $user->update();
                            $order['affilate_user'] = $user->name;
                            $order['affilate_charge'] = $sub;
                        }
                        $order->save();


            if($order->dp == 1) {
                $track = new OrderTrack;
                $track->title = 'Completed';
                $track->text = 'Your order has completed successfully.';
                $track->order_id = $order->id;
                $track->save();
            }
            else {
                $track = new OrderTrack;
                $track->title = 'Pending';
                $track->text = 'You have successfully placed your order.';
                $track->order_id = $order->id;
                $track->save();
            }
                        
                        $notification = new Notification;
                        $notification->order_id = $order->id;
                        $notification->save();
                        
                        if($paypal_data['coupon_id'] != "") {
                          $coupon = Coupon::findOrFail($paypal_data['coupon_id']);
                          $coupon->used++;
                          if($coupon->times != null) {
                              $i = (int)$coupon->times;
                              $i--;
                              $coupon->times = (string)$i;
                          }
                          $coupon->update();

                        }
                        foreach($cart->items as $prod)
                        {
                            $x = (string)$prod['stock'];
                            if($x != null)
                            {
                                $product = Product::findOrFail($prod['item']['id']);
                                $product->stock =  $prod['stock'];
                                $product->update();                
                            }
                        }

            foreach($cart->items as $prod)
            {
                $x = (string)$prod['size_qty'];
                if(!empty($x))
                {
                    $product = Product::findOrFail($prod['item']['id']);
                    $x = (int)$x;
                    $x = $x - $prod['qty'];
                    $temp = $product->size_qty;
                    $temp[$prod['size_key']] = $x;
                    $temp1 = implode(',', $temp);
                    $product->size_qty =  $temp1;
                    $product->update();               
                }
            }


            foreach($cart->items as $prod)
            {
                $x = (string)$prod['stock'];
                if($x != null)
                {

                    $product = Product::findOrFail($prod['item']['id']);
                    $product->stock =  $prod['stock'];
                    $product->update();  
                    if($product->stock <= 5)
                    {
                        $notification = new Notification;
                        $notification->product_id = $product->id;
                        $notification->save();                    
                    }              
                }
            }


            $notf = null;

            foreach($cart->items as $prod)
            {
                if($prod['item']['user_id'] != 0)
                {
                    $vorder =  new VendorOrder;
                    $vorder->order_id = $order->id;
                    $vorder->user_id = $prod['item']['user_id'];
                    $notf[] = $prod['item']['user_id'];
                    $vorder->qty = $prod['qty'];
                    $vorder->price = $prod['price'];
                    $vorder->order_number = $order->order_number;             
                    $vorder->save();
                }

            }

            if(!empty($notf))
            {
                $users = array_unique($notf);
                foreach ($users as $user) {
                    $notification = new UserNotification;
                    $notification->user_id = $user;
                    $notification->order_number = $order->order_number;
                    $notification->save();    
                }
            }


            $gs = Generalsetting::find(1);

            //Sending Email To Buyer

            if($gs->is_smtp == 1)
            {
            $data = [
                'to' => $paypal_data['email'],
                'type' => "new_order",
                'cname' => $paypal_data['name'],
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'wtitle' => "",
                'onumber' => $item_number
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoOrderMail($data,$order->id);            
            }
            else
            {
              $to = $paypal_data['email'];
              $subject = "Your Order Placed!!";
              $msg = "Hello ".$paypal_data['name']."!\nYou have placed a new order.\nYour order number is ".$item_number.".Please wait for your delivery. \nThank you.";
                $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
              mail($to,$subject,$msg,$headers);            
            }
            //Sending Email To Admin
            if($gs->is_smtp == 1)
            {
                $data = [
                    'to' => $gs->email,
                    'subject' => "New Order Recieved!!",
                    'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$item_number.".Please login to your panel to check. <br>Thank you.",
                ];

                $mailer = new GeniusMailer();
                $mailer->sendCustomMail($data);            
            }
            else
            {
              $to = $gs->email;
              $subject = "New Order Recieved!!";
              $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$item_number.".Please login to your panel to check. \nThank you.";
                $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
              mail($to,$subject,$msg,$headers);
            }


            Session::put('temporder',$order);
            Session::put('tempcart',$cart);
            Session::forget('cart');
            Session::forget('already');
            Session::forget('coupon');
            Session::forget('coupon_total');
            Session::forget('coupon_total1');
            Session::forget('coupon_percentage');
            Session::forget('cart');
            Session::forget('paypal_data');
            Session::forget('paypal_items');
                return redirect($success_url);
            }
            else {
                return redirect($cancel_url);
            }

    }

      public function paycancle() {
          $this->code_image();
          return response()->json([
            'success' => false,
            'msg' => 'Payment Cancelled.'
          ], 201);
      }

      public function payreturn() {
        $this->code_image();
        if(Session::has('tempcart')){
        $oldCart = Session::get('tempcart');
        $tempcart = new Cart($oldCart);
        $order = Session::get('temporder');
        }
        else{
            $tempcart = '';
            return null;
        }

        return response()->json([
          'success' => false,
          'data' => compact('tempcart','order')
        ], 201);
      }

      // Capcha Code Image
      private function  code_image() {

        $actual_path = str_replace('project','',base_path());
        $image = imagecreatetruecolor(200, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image,0,0,200,50,$background_color);

        $pixel = imagecolorallocate($image, 0,0,255);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixel);
        }

        $font = $actual_path.'assets/front/fonts/NotoSans-Bold.ttf';
        $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($allowed_letters);
        $letter = $allowed_letters[rand(0, $length-1)];
        $word='';
        //$text_color = imagecolorallocate($image, 8, 186, 239);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $cap_length=6;// No. of character in image
        for ($i = 0; $i< $cap_length;$i++)
        {
            $letter = $allowed_letters[rand(0, $length-1)];
            imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
            $word.=$letter;
        }
        $pixels = imagecolorallocate($image, 8, 186, 239);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixels);
        }
        session(['captcha_string' => $word]);
        imagepng($image, $actual_path."assets/images/capcha_code.png");
      }

  }
