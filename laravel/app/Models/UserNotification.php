<?php

  namespace App\Model;

  use Illuminate\Database\Eloquent\Model;

  class UserNotification extends Model
  {
    public function countOrder($id) {
      return UserNotification::where('user_id', '=', $id)->where('is_read', '=', 0)->get()->count();
    }
  }
  