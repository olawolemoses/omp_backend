<?php

  namespace App\Http\Controllers\Front;

  use App\Classes\GeniusMailer;
  use App\Http\Controllers\Controller;
  use App\Models\Cart;
  use App\Models\Coupon;
  use App\Models\Currency;
  use App\Models\Generalsetting;
  use App\Models\Notification;
  use App\Models\Order;
  use App\Models\OrderTrack;
  use App\Models\PaymentGateway;
  use App\Models\Pickup;
  use App\Models\Product;
  use App\Models\User;
  use App\Models\UserNotification;
  use Illuminate\Http\Request;
  use App\Models\VendorOrder;
  use Auth;
  use Session;
  use Log;


  class PaystackCtrl extends Controller
  { 

    public function store(Request $request) {

        Log::info('I have access: ' .$request);

        if ($request->pass_check) {
            $users = User::where('email', '=', $request->personal_email)->get();
            if (count($users) == 0) {
                if ($request->personal_pass == $request->personal_confirm) {
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
                    return response()->json([
                        'success' => false,
                        'msg' => 'Confirm Password Doesn\'t Match.'
                      ], 201);
                }
            } else {
              return response()->json([
                'success' => false,
                'msg' => 'This Email Already Exist.'
              ], 201);
            }
        }
        
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', '=', 1)->first();
        }

        $gs = Generalsetting::findOrFail(1);

        $items = $request['items'];
        
        foreach ($items as $key => $prod) {
            if (!empty($prod['item']['license']) && !empty($prod['item']['license_qty'])) {
                foreach ($prod['item']['license_qty']as $ttl => $dtl) {
                    if ($dtl != 0) {
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
                        $cart->updateLicense($prod['item']['id'], $license);
                        Session::put('cart', $cart);
                        break;
                    }
                }
            }
        }
        $order = new Order;
        // $success_url = action('Front\PaymentController@payreturn');
        $item_name = $gs->title." Order";
        $item_number = str_random(4).time();
        $order['user_id'] = $request['user_id'];
        $order['cart'] = utf8_encode(bzcompress(serialize($items), 9));
        $order['total_quantity'] = $request['total_quantity'];
        $order['pay_amount'] = round($request['total'] / $curr->value, 2)  + $request['shipping_cost'] + $request['packing_cost'];
        $order['method'] = $request->method;
        $order['shipping'] = $request['billing']['shipping_method'];
        // $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request['billing']['email'];
        $order['customer_name'] = $request['billing']['name'];
        $order['shipping_cost'] = $request['shipping_cost'];
        $order['packing_cost'] = $request['packing_cost'];
        $order['tax'] = $request['tax'];
        $order['customer_phone'] = $request['billing']['phone'];
        $order['order_number'] = str_random(4).time();
        $order['customer_address'] = $request['billing']['address'];
        $order['customer_country'] = $request['billing']['country'];
        $order['customer_city'] = $request['billing']['city'];
        $order['customer_zip'] = $request['billing']['zip'];
        $order['shipping_email'] = $request['shipping']['s_email'];
        $order['shipping_name'] = $request['shipping']['s_name'];
        $order['shipping_phone'] = $request['shipping']['s_phone'];
        $order['shipping_address'] = $request['shipping']['s_address'];
        $order['shipping_country'] = $request['shipping']['s_country'];
        $order['shipping_city'] = $request['shipping']['s_city'];
        $order['shipping_zip'] = $request['shipping']['s_zip'];
        $order['order_note'] = $request['shipping']['s_notes'];
        // $order['coupon_code'] = $request->coupon_code;
        // $order['coupon_discount'] = $request->coupon_discount;
        $order['dp'] = $request->dp;
        $order['payment_status'] = "Pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['payment_status'] = "Completed";
        // $order['txnid'] = $request->ref_id;
        $order['dp'] = $request->dp;
        // $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        // $order['vendor_packing_id'] = $request->vendor_packing_id;
        
        if ($order['dp'] == 1) {
            $order['status'] = 'completed';
        }

        if (Session::has('affilate')) {
            $val = $request->total / 100;
            $sub = $val * $gs->affilate_charge;
            $user = User::findOrFail(Session::get('affilate'));
            $user->affilate_income += $sub;
            $user->update();
            $order['affilate_user'] = $user->name;
            $order['affilate_charge'] = $sub;
        }
        // dd($order);
        $order->save();

        if ($order->dp == 1) {
            $track = new OrderTrack;
            $track->title = 'Completed';
            $track->text = 'Your order has completed successfully.';
            $track->order_id = $order->id;
            $track->save();
        } else {
            $track = new OrderTrack;
            $track->title = 'Pending';
            $track->text = 'You have successfully placed your order.';
            $track->order_id = $order->id;
            $track->save();
        }
        
        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
        if ($request->coupon_id != "") {
            $coupon = Coupon::findOrFail($request->coupon_id);
            $coupon->used++;
            if ($coupon->times != null) {
                $i = (int)$coupon->times;
                $i--;
                $coupon->times = (string)$i;
            }
            $coupon->update();
        }

        foreach ($items as $prod) {
            $x = (string)$prod['size_qty'];
            if (!empty($x)) {
                $product = Product::findOrFail($prod['item']['id']);
                $x = (int)$x;
                $x = $x - $prod['qty'];
                $temp = (array)  $product->size_qty;
                $temp[$prod['size_key']] = $x;
                $temp1 = implode(',', $temp);
                $product->size_qty =  $temp1;
                $product->update();
            }
        }

        foreach ($items as $prod) {
            $x = (string)$prod['stock'];
            if ($x != null) {
                $product = Product::findOrFail($prod['item']['id']);
                $product->stock =  $prod['stock'];
                $product->update();
                if ($product->stock <= 5) {
                    $notification = new Notification;
                    $notification->product_id = $product->id;
                    $notification->save();
                }
            }
        }

        $notf = null;

        foreach ($items as $prod) {
            if ($prod['item']['user_id'] != 0) {
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

        if (!empty($notf)) {
            $users = array_unique($notf);
            foreach ($users as $user) {
                $notification = new UserNotification;
                $notification->user_id = $user;
                $notification->order_number = $order->order_number;
                $notification->save();
            }
        }

        Session::put('temporder', $order);
        Session::put('tempcart', $items);

        Session::forget('cart');

        Session::forget('already');
        Session::forget('coupon');
        Session::forget('coupon_total');
        Session::forget('coupon_total1');
        Session::forget('coupon_percentage');
        
        //Sending Email To Buyer

        if ($gs->is_smtp == 1) {
            $data = [
                'to' => $request->email,
                'type' => "new_order",
                'cname' => $request->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'wtitle' => "",
                'onumber' => $order->order_number,
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoOrderMail($data, $order->id);
        } else {
            $to = $request->email;
            $subject = "Your Order Placed!!";
            $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.";
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            mail($to, $subject, $msg, $headers);
        }
        //Sending Email To Admin
        if ($gs->is_smtp == 1) {
            $data = [
                'to' => $gs->email,
                'subject' => "New Order Recieved!!",
                'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.",
            ];

            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);
        } else {
            $to = $gs->email;
            $subject = "New Order Recieved!!";
            $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order->order_number.".Please login to your panel to check. \nThank you.";
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            mail($to, $subject, $msg, $headers);
        }
    }
  }
