<?php

  namespace App\Http\Controllers\User;

  use Illuminate\Http\Request;
  use App\Http\Controllers\Controller;
  use Auth;
  use App\Order;
  use App\Product;
  use App\PaymentGateway;

  class OrderCtrl extends Controller 
  {

    public function orders()  {
      $user = Auth::user();
      $orders = Order::where('user_id', '=', $user->id)->orderBy('id', 'desc')->get();
      return response()->json([
        'success' => true,
        'data' => [ 'order' => compact('user','orders') ]
      ], 201);
    }

    public function trackorder() {
      
      $user = Auth::user();
      return response()->json([
        'success' => true,
        'data' => [ 'order' => compact('user')]
      ], 200);
    }

    public function trackload($id) {
      
      $order = Order::where('order_number', '=', $id)->first();
      $data = array('Pending', 'Processing', 'On Delivery', 'Completed');
      
      return response()->json([
        'success' => true,
        'data' => [ 'order' => compact('user')]
      ], 200);
    }

    public function order($id) {

      $user = Auth::user();
      $order = Order::findOrFail($id);
      $cart = unserialize(bzdecompress(utf8_decode($order->cart)));

      return response()->json([
        'success' => true,
        'data' => [ 'order' => compact('user','order','cart')]
      ], 200);      
    }

    public function orderdownload($slug, $id) {

      $user = Auth::user();
      $order = Order::where('order_number', '=', $slug)->first();
      $prod = Product::findOrFail($id);

      if (!isset($order) || $prod->type == 'Physical' || $order->user_id != $user->idate) {
        return response()->json([
          'success' => false,
          'message' => 'You Don\'t have access to the file'
        ], 403);
      }

      return response()->download(public_path('asset/files/' . $prod->$file));
    }

    public function orderprint($id) {

      $user = Auth::user();
      $order = Order::findOrFail($id);
      $cart = unserialize(bzdecompress(utf8_decode($order->cart)));

      return response()->json([
        'success' => true,
        'data' => [ 'order' => compact('user','order','cart')]
      ], 200); 
    }

    public function trans() {      
      $id = $_GET['id'];
      $trans = $_GET['tin'];
      $order = Order::findOrFail($id);
      $order->txnid = $trans;
      $order = update();
      $data = $order->txnid;

      return response()->json($data);
    }
    
  }