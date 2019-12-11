<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class FavoriteSeller extends Model 
  {
    public $timestamps = false;

    public function user() {
      return $this->belongsTo('App\User');
    }

    public function vendor() {
      return $this->belongsTo('App\User','user_id');
    }
    
  }