<?php

  namespace App\Http\Controllers\Vendor;

  use Illuminate\Http\Request;
  use App\Http\Controllers\Controller;
  use App\UserNotification;

  class NotificationCtrl extends Controller
  {

    public function order_notf_count($id) {
      $data =  UserNotification::where('user_id', '=',$id)->where('is_read', '=', 0)->get()->count();
      return response()->json($data);
    }

    public function order_notf_clear($id) {
      $data =  UserNotification::where('user_id', '=',$id);
      $data->delete();
    }

    public function order_notf_show($id) {
      $datas =  UserNotification::where('user_id', '=',$id)->get();
      if ($datas->count() > 0) {
        foreach ($datas as $data) {
          $data->is_read = 1;
          $data->update();
        }
      }
      return response()->json([
        'success' => true,
        'data' => compact('data')
      ], 201);
    }

  }