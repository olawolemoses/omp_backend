<?php

  namespace App\Http\Controllers\Vendor;

  use Illuminate\Http\Request;
  use App\Http\Controllers\Controller;
  use Auth;
  use App\Order;
  use App\VendorOrder;

  class OrderCtrl extends Controller
  {
      
      public function index() {
          $user = Auth::user();
          $orders = VendorOrder::where('user_id', '=', $user->id)->orderBy('id', 'desc')->get()->groupBy('order_number');

          return response()->json([
            'success' => true,
            'data' => compact('user','orders')
          ], 201);
      }




      public function show($slug) {
        $user = Auth::user();
        $order = Order::where('order_number', '=', $slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));

        return response()->json([
          'success' => true,
          'data' => compact('user','order','cart')
        ], 201);
      }

      public function license(Request $request, $slug) {

        $order = Order::where('order_number', '=', $slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $cart->items[$request->license_key]['license'] = $request->license;
        $order->cart = utf8_encode(bzcompress(serialize($cart), 9));
        $order->update();
        $msg = 'Successfully Changed The License Key.';
        return response()->json($msg);
      }



      public function invoice($slug) {
        $user = Auth::user();
        $order = Order::where('order_number', '=', $slug)->first();
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));

        return response()->json([
          'success' => true,
          'data' => compact('user','order','cart')
        ], 201);
      }

      public function printpage($slug) {
          $user = Auth::user();
          $order = Order::where('order_number', '=', $slug)->first();
          $cart = unserialize(bzdecompress(utf8_decode($order->cart)));

          return response()->json([
            'success' => true,
            'data' => compact('user','order','cart')
          ], 201);
      }

      public function status($slug, $status) {

        $mainorder = VendorOrder::where('order_number', '=', $slug)->first();
        if ($mainorder->status == "completed") {
          return response()->json([
            'success' => true,
            'msg' => 'This Order is Already Completed'
          ], 201);
        } else {
            $user = Auth::user();
            $order = VendorOrder::where('order_number', '=', $slug)->where('user_id', '=', $user->id)->update(['status' => $status]);

            return response()->json([
              'success' => true,
              'msg' => 'Order Status Updated Successfully'
            ], 201);
        }
      }
  }
