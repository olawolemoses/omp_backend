<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class OrderTrack extends Model
  {
    protected $fillable = ['order_id', 'title','text'];

    public function order() {
      return $this->belongsTo('App\Order', 'order_id');
    }

  }
  