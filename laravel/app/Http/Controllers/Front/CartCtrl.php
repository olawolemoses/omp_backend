<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Coupon;
use App\Models\Generalsetting;
use Session;
use Log;

class CartCtrl extends Controller
{
    public function cart(Request $request)
    {

        // $this->code_image();

        if (!$request->session()->has('cart')) {
            return null;
        }
        if ($request->session()->has('already')) {
            $request->session()->forget('already');
        }
        if ($request->session()->has('coupon')) {
            $request->session()->forget('coupon');
        }
        if ($request->session()->has('coupon_total')) {
            $request->session()->forget('coupon_total');
        }
        if ($request->session()->has('coupon_total1')) {
            $request->session()->forget('coupon_total1');
        }
        if ($request->session()->has('coupon_percentage')) {
            $request->session()->forget('coupon_percentage');
        }

        $gs = Generalsetting::findOrFail(1);
        $oldcart = $request->session()->get('cart');
        $cart = new Cart($oldcart);
        $products = $cart->items;
        $totalPrice = $cart->totalPrice;
        $minTotal = $totalPrice;
        $tx = $gs->tax;

        if ($tx != 0) {
            $tax = ($totalPrice / 100) * $tx;
            $mainTotal = $totalPrice + $tax;
        }

        return response()->json([
            'success' => true,
            'data' => compact('products', 'totalPrice', 'mainTotal', 'tx')
        ], 201);
    }

    public function addtocart(Request $request, $id)
    {
        $prod = Product::where('id', '=', $id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount']);

        if (!empty($prod->license_qty)) {
            $lcheck = 1;
            foreach ($prod->license_qty as $ttl => $dtl) {
                if ($dtl < 1) {
                    $lcheck = 0;
                } else {
                    $lcheck = 1;
                    break;
                }
            }
            if ($lcheck == 0) {
                return 0;
            }
        }
        $size = '';
        if (!empty($prod->size)) {
            $size = $prod->size[0];
        }

        if ($prod->user_id != 0) {
            $gs = Generalsetting::findOrFail(1);
            $prc = $prod->price + $gs->fixed_commission + ($prod->price / 100) * $gs->percentage_commission;
            // $prod->price = round($prc, 2);
        }

        $oldcart = $request->session()->has('cart') ? $request->session()->get('cart') : null;
        $cart = new Cart($oldcart);

        $cart->add($prod, $prod->id, $size);
        if ($cart->items[$id.$size]['dp'] == 1) {
            return 'digital';
        }
        if ($cart->items[$id.$size]['stock'] < 0) {
            return 0;
        }
        if (!empty($cart->items[$id.$size]['size_qty'])) {
            if ($cart->items[$id.$size]['qty'] > $cart->items[$id.$size]['size_qty']) {
                return 0;
            }
        }
        $cart->totalPrice = 0;
        foreach ($cart->items as $data) {
            $cart->totalPrice += $data['price'];
        }
        $request->session()->put('cart', $cart);
    }

    public function addcart(Request $request, $id)
    {
        $prod = Product::where('id', '=', $id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount']);

        if (!empty($prod->license_qty)) {
            $lcheck = 1;
            foreach ($prod->license_qty as $ttl => $dtl) {
                if ($dtl < 1) {
                    $lcheck = 0;
                } else {
                    $lcheck = 1;
                    break;
                }
            }
            if ($lcheck == 0) {
                return 0;
            }
        }
        $size = '';
        if (!empty($prod->size)) {
            $size = $prod->size[0];
        }

        if ($prod->user_id != 0) {
            $gs = Generalsetting::findOrFail(1);
            $prc = $prod->price + $gs->fixed_commission + ($prod->price / 100) * $gs->percentage_commission;
            // $prod->price = round($prc, 2);
        }

        $oldcart = $request->session()->has('cart') ? $request->session()->get('cart') : null;
        $cart = new Cart($oldcart);

        $cart->add($prod, $prod->id, $size);
        if ($cart->items[$id.$size]['dp'] == 1) {
            return 'digital';
        }
        if ($cart->items[$id.$size]['stock'] < 0) {
            return 0;
        }
        if (!empty($cart->items[$id.$size]['size_qty'])) {
            if ($cart->items[$id.$size]['qty'] > $cart->items[$id.$size]['size_qty']) {
                return 0;
            }
        }

        $cart->totalPrice = 0;
        foreach ($cart->items as $data) {
            $cart->totalPrice += $data['price'];
        }
        $request->session()->put('cart', $cart);
        $data[0] = count($cart->items);
        return response()->json($data);
    }

    public function addnumcart(Request $request, $item)
    {   
        Log::info($item);
        dd($item);

        $id = $item->id;
        $qty =  $item->qty;
        $size =  $item->size;
        $color =  $item->color;
        $size_qty =  $item->size_qty;
        $size_qty =  $item->size_key;
        $size_price =  $item->size_price;
        $price = 0;
        $price += (double)$size_price;

        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount']);


        if($prod->user_id != 0){
        $gs = Generalsetting::findOrFail(1);
        $prc = $prod->price + $gs->fixed_commission + ($prod->price/100) * $gs->percentage_commission ;
        $prod->price = round($prc,2);
        }

        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }                    
            }
                if($lcheck == 0)
                {
                    return 0;            
                }
        }
        if(empty($size))
        {
            if(!empty($prod->size))
            { 
            $size = $prod->size[0];
            }          
        }
 


        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->addnum($prod, $prod->id, $qty, $size,$color,$size_qty,$size_price,$size_key);
        if($cart->items[$id.$size]['dp'] == 1)
        {
            return 'digital';
        }
        if($cart->items[$id.$size]['stock'] < 0)
        {
            return 0;
        }
        if(!empty($cart->items[$id.$size]['size_qty']))
        {
            if($cart->items[$id.$size]['qty'] > $cart->items[$id.$size]['size_qty'])
            {
                return 0;
            }           
        }

        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];        
        Session::put('cart',$cart);
        $data[0] = count($cart->items);   
        return response()->json($data);

    }

    public function addbyone(Request $request, $increment)
    {   

        if ($request->session()->has('coupon')) {
            $request->session()->forget('coupon');
        }

        $gs = Generalsetting::findOrFail(1);
        
        if ($request->session()->has('currency')) {
            $curr = Currency::find($request->session()->get('currency'));
        } else {
            $curr = Currency::where('is_default', '=', 1)->first();
        }

        $id = $increment->item->id;
        $itemid = $increment->item->id;
        $size_qty =  $increment->item->size_qty;
        $size_price =  $increment->item->size_price;
        
        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount']);

        if($prod->user_id != 0){
        $gs = Generalsetting::findOrFail(1);
        $prc = $prod->price + $gs->fixed_commission + ($prod->price/100) * $gs->percentage_commission ;
        $prod->price = round($prc,2);
        }

        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }
            }
                if($lcheck == 0)
                {
                    return 0;
                }
        }
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->adding($prod, $itemid,$size_qty,$size_price);
        if($cart->items[$itemid]['stock'] < 0)
        {
            return 0;
        }
        if(!empty($size_qty))
        {
            if($cart->items[$itemid]['qty'] > $cart->items[$itemid]['size_qty'])
            {
                return 0;
            }
        }
        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];
        Session::put('cart',$cart);
        $data[0] = $cart->totalPrice;

        $data[3] = $data[0];
        $tx = $gs->tax;
        if($tx != 0)
        {
            $tax = ($data[0] / 100) * $tx;
            $data[3] = $data[0] + $tax;
        }

        $data[1] = $cart->items[$itemid]['qty'];
        $data[2] = $cart->items[$itemid]['price'];
        $data[0] = round($data[0] * $curr->value,2);
        $data[2] = round($data[2] * $curr->value,2);
        if($gs->currency_format == 0){
            $data[0] = $curr->sign.$data[0];
            $data[2] = $curr->sign.$data[2];
            $data[3] = $curr->sign.$data[3];
        }
        else{
            $data[0] = $data[0].$curr->sign;
            $data[2] = $data[2].$curr->sign;
            $data[3] = $data[3].$curr->sign;
        }
        return response()->json($data);
    }

    public function reducebyone(Request $request, $decrement)
    {
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }
        
        $gs = Generalsetting::findOrFail(1);

        if (Session::has('currency')) 
        {
            $curr = Currency::find(Session::get('currency'));
        }
        else
        {
            $curr = Currency::where('is_default','=',1)->first();
        }

        $id = $increment->item->id;
        $itemid = $increment->item->id;
        $size_qty =  $increment->item->size_qty;
        $size_price =  $increment->item->size_price;

        $prod = Product::where('id','=',$id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount']);
        if($prod->user_id != 0){
        $gs = Generalsetting::findOrFail(1);
        $prc = $prod->price + $gs->fixed_commission + ($prod->price/100) * $gs->percentage_commission ;
        $prod->price = round($prc,2);
        }

        
        if(!empty($prod->license_qty))
        {
        $lcheck = 1;
            foreach($prod->license_qty as $ttl => $dtl)
            {
                if($dtl < 1)
                {
                    $lcheck = 0;
                }
                else
                {
                    $lcheck = 1;
                    break;
                }                    
            }
                if($lcheck == 0)
                {
                    return 0;            
                }
        }
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->reducing($prod, $itemid,$size_qty,$size_price);
        $cart->totalPrice = 0;
        foreach($cart->items as $data)
        $cart->totalPrice += $data['price'];    
        
        Session::put('cart',$cart);
        $data[0] = $cart->totalPrice;

        $data[3] = $data[0];
        $tx = $gs->tax;
        if($tx != 0)
        {
            $tax = ($data[0] / 100) * $tx;
            $data[3] = $data[0] + $tax;
        }  

        $data[1] = $cart->items[$itemid]['qty']; 
        $data[2] = $cart->items[$itemid]['price'];
        $data[0] = round($data[0] * $curr->value,2);
        $data[2] = round($data[2] * $curr->value,2);
        if($gs->currency_format == 0){
            $data[0] = $curr->sign.$data[0];
            $data[2] = $curr->sign.$data[2];
            $data[3] = $curr->sign.$data[3];
        }
        else{
            $data[0] = $data[0].$curr->sign;
            $data[2] = $data[2].$curr->sign;
            $data[3] = $data[3].$curr->sign;
        }       
        
        return response()->json($data);
    }

    public function upcolor()
    {
        $id = $_GET['id'];
        $color = $_GET['colo$color'];
        $prod = Product::where('id', '=', $id)->first(['id','user_id','slug','name','photo','size','size_qty','size_price','color','price','stock','type','file','link','license','license_qty','measure','whole_sell_qty','whole_sell_discount']);

        $oldcart = $request->session()->has('cart') ? $request->session()->get('cart') : null;
        $cart = new Cart($oldcart);
        $cart->updateColor($prod, $id, $color);
        $request->session()->put('cart', $cart);
    }

    public function removecart(Request $request, $id)
    {
        $gs = Generalsetting::findOrFail(1);
        if ($request->session()->has('currency')) {
            $curr = Currency::find($request->session()->get('currency'));
        } else {
            $curr = Currency::where('is_default', '=', 1)->first();
        }

        $oldcart = $request->session()->has('cart') ? $request->session()->get('cart') : null;
        $cart = new Cart($oldcart);

        $cart->removeItem($id);
        if (count((array)$cart->items) > 0) {
            $request->session()->put('cart', $cart);
            $data[0] = $cart->totalPrice;
            $data[3] = $data[0];
            $tx = $gs->tax;

            if ($tx != 0) {
                $tax = ($data[0] / 100) * $tx;
                $data[3] = $data[0] + $tax;
            }
            if ($gs->currency_format == 0) {
                $data[0] =  $curr->sign.round($data[0] * $curr->value, 2);
                $data[3] =  $curr->sign.round($data[3] * $curr->value, 2);
            } else {
                $data[0] =  $curr->sign.round($data[0] * $curr->value, 2);
                $data[3] =  $curr->sign.round($data[3] * $curr->value, 2);
            }
            $data[1] = count($cart->items);
            return response()->json();
        } else {
            $request->session()->forget('cart');
            $request->session()->forget('already');
            $request->session()->forget('coupon');
            $request->session()->forget('coupon_total');
            $request->session()->forget('coupon_total1');
            $request->session()->forget('coupon_percentage');

            $data = 0;
            return response()->json($data);
        }
    }

    public function coupon()
    {
        $gs = Generalsetting::findOrFail(1);
        $code = $_GET['code'];
        $total = (float) preg_replace('\[^0-9\.]/ui', '', $_GET['total']);
        $fnd = Coupon::where('code', '=', $code)->get()->count();
        if ($fnd < 1) {
            return response()->json(0);
        } else {
            $coupon = Coupon::where('code', '=', $code)->first();
            if (Session::has('currency')) {
                $curr = Currency::find(Session::get('currency'));
            } else {
                $curr = Currency::where('is_default', '=', 1)->first();
            }
            if ($coupon->times != null) {
                if ($coupon->times == 0) {
                    return response()->json(0);
                }
            }

            $today = date('Y-m-d');
            $from = date('Y-m-d', strtotime($coupon->start_date));
            $to = date('Y-m-d', strtotime($coupon->end_date));
            if ($from <= $today && $to >= $today) {
                if ($coupon->status == 1) {
                    $oldcart = $request->session()->has('cart') ? $request->session()->get('cart') : null;
                    $val = $request->session()->has('already') ? $request->session()->get('already') : null;
                    if ($val == code) {
                        return response()->json(2);
                    }
                    $cart = new Cart($oldcart);
                    if ($coupon->type == 0) {
                        $request->session()->put('already', $code);
                        $coupon->price = (int) $coupon->price;
                        $val = $total / 100;
                        $sub = $val * $coupon->price;
                        $total = $total - $sub;
                        $data[0] = round($total, 2);
                        if ($gs->currency_format == 0) {
                            $data[0] = $curr->sign.$data[0];
                        } else {
                            $data[0] = $data[0].$curr->sign;
                        }
                        $request->session()->put('coupon', $data[2]);
                        $request->session()->put('coupon_code', $code);
                        $request->session()->put('coupon_id', $coupon->id);
                        $request->session()->put('coupon_total1', $data[0]);
                        $request->session()->forget('coupon_total');
                        $data[0] = round($total, 2);
                        $data[1] = $code;
                        $data[2] = round($sub, 2);
                        $data[3] = $coupon->id;
                        $data[4] = $coupon->price."%";
                        $data[5] = 1;

                        $request->session()->put('coupon_percentage', $data[4]);
                        return response()->json($data);
                    } else {
                        $request->session()->put('already', $code);
                        $total = $total - round($coupon->price * $curr->value, 2);
                        $data[0] = round($total, 2);
                        $data[1] = $code;
                        $data[2] = round($coupon->price * $curr->value, 2);
                        $data[3] = $coupon->id;
                        if ($gs->currency_format == 0) {
                            $data[4] = 0;
                            $data[0] = $curr->sign.$data[0];
                        } else {
                            $data[4] = 0;
                            $data[0] = $data[0].$curr->sign;
                        }
                        $request->session()->put('coupon', $data[2]);
                        $request->session()->put('coupon_code', $code);
                        $request->session()->put('coupon_id', $coupon->id);
                        $request->session()->put('coupon_total1', $data[0]);
                        $request->session()->forget('coupon_total');
                        $data[0] = round($total, 2);
                        $data[1] = $code;
                        $data[2] = round($sub, 2);
                        $data[3] = $coupon->id;
                        $data[4] = $coupon->price."%";
                        $data[5] = 1;

                        $request->session()->put('coupon_percentage', $data[4]);
                        return response()->json($data);
                    }
                } else {
                    return response()->json(0);
                }
            } else {
                return response()->json(0);
            }
        }
    }

    public function couponcheck()
    {
        $gs = Generalsetting::findOrFail(1);
        $code = $_GET['code'];
        $total = $_GET['total'];
        $fnd = Coupon::where('code', '=', $code)->get()->count();
        if ($fnd < 1) {
            return response()->json(0);
        } else {
            $coupon = Coupon::where('code', '=', $code)->first();
            if ($request->session()->has('currency')) {
                $curr = Currency::find($request->session()->get('currency'));
            } else {
                $curr = Currency::where('is_default', '=', 1)->first();
            }
            if ($coupon->times != null) {
                if ($coupon->times == 0) {
                    return response()->json(0);
                }
            }

            $today = date('Y-m-d');
            $from = date('Y-m-d', strtotime($coupon->start_date));
            $to = date('Y-m-d', strtotime($coupon->end_date));
            if ($from <= $today && $to >= $today) {
                if ($coupon->status == 1) {
                    $oldcart = $request->session()->has('cart') ? $request->session()->get('cart') : null;
                    $val = $request->session()->has('already') ? $request->session()->get('already') : null;
                    if ($val == code) {
                        return response()->json(2);
                    }
                    $cart = new Cart($oldcart);
                    if ($coupon->type == 0) {
                        $request->session()->put('already', $code);
                        $coupon->price = (int) $coupon->price;

                        $oldCart = $request->session()->get('cart');
                        $cart = new Cart($oldCart);

                        $total = $total - $_GET['shipping_cost'];
                        $val = $total / 100;
                        $sub = $val * $coupon->price;
                        $total = $total - $sub;
                        $total = $total + $_GET['shipping_cost'];
                        $data[0] = round($total, 2);
                        $data[1] = $code;
                        $data[2] = round($sub, 2);
                        if ($gs->currency_format == 0) {
                            $data[0] = $curr->sign.$data[0];
                        } else {
                            $data[0] = $data[0].$curr->sign;
                        }
                        $request->session()->put('coupon', $data[2]);
                        $request->session()->put('coupon_code', $code);
                        $request->session()->put('coupon_id', $coupon->id);
                        $request->session()->put('coupon_total', $data[0]);
                        $data[0] = round($total, 2);
                        $data[1] = $code;
                        $data[2] = round($sub, 2);
                        $data[3] = $coupon->id;
                        $data[4] = $coupon->price."%";
                        $data[5] = 1;

                        $request->session()->put('coupon_percentage', $data[4]);
                        return response()->json($data);
                    } else {
                        $request->session()->put('already', $code);
                        $total = $total - round($coupon->price * $curr->value, 2);
                        $data[0] = round($total, 2);
                        $data[1] = $code;
                        $data[2] = round($coupon->price * $curr->value, 2);
                        $request->session()->put('coupon', $data[2]);
                        $request->session()->put('coupon_code', $code);
                        $request->session()->put('coupon_id', $coupon->id);
                        $request->session()->put('coupon_total', $data[0]);
                        $data[3] = $coupon->id;
                        if ($gs->currency_format == 0) {
                            $data[4] = $curr->sign.$data[2];
                            $data[0] = $curr->sign.$data[0];
                        } else {
                            $data[4] = $data[2].$curr->sign;
                            $data[0] = $data[0].$curr->sign;
                        }

                        Session::put('coupon_percentage', 0);
                        $data[5] = 1;
                        return response()->json($data);
                    }
                } else {
                    return response()->json(0);
                }
            } else {
                return response()->json(0);
            }
        }
    }

    public function code_image()
    {
        $actual_path = str_replace('project', '', base_path());
        $image = imagecreatetruecolor(200, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, 200, 50, $background_color);

        $pixel = imagecolorallocate($image, 0, 0, 255);
        for ($i=0; $i < 500; $i++) {
            imagesetpixel($image, rand()%200, rand%50, $pixel);
        }

        $font = $actual_path.'asset/front/NotoSans-Bold.tff';
        $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($allowed_letters);
        $letter = $allowed_letters[rand(0, $length - 1)];
        $word = '';
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $cap_length = 6;

        for ($i=0; $i < $cap_length; $i++) {
            $letter = $allowed_letters[rand(0, $length - 1)];
            imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
            $word .=$letter;
        }
        $pixels = iamgecolorallocate($image, 8, 186, 239);
        for ($i=0; $i < 500; $i++) {
            # code...
            iamgecolorallocate($image, rand()%200, rand()%50, $pixels);
        }

        session(['capthcha_string' => $word]);
        imagepng($image, $actual_path.'asset/images/capcha_code.png');
    }
}
