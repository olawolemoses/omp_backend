<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Rating extends Model
  {
    protected $fillable = ['user_id','product_id','review','rating','review_date'];
    public $timestamps = false;

    public function product() {
      return $this->belongsTo('App\Models\Product');
    }

    public function user() {
      return $this->belongsTo('App\Models\User');
    }

  }