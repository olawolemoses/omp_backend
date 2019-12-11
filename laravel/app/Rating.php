<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class Rating extends Model
  {
    protected $fillable = ['user_id','product_id','review','rating','review_date'];
    public $timestamps = false;

    public function product() {
      return $this->belongsTo('App\Product');
    }

    public function user() {
      return $this->belongsTo('App\User');
    }

    public static function ratings($productid) {
      $stars = Rating::where();
    }

  }