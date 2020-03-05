<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Comment extends Model 
  {
    protected $fillable = ['product_id', 'user_id','text'];

    public function user() {
    	return $this->belongsTo('App\Models\User');
    }

    public function product() {
    	return $this->belongsTo('App\Models\Product');
    }

    public function replies()	{
      return $this->hasMany('App\Models\Reply');
    }

  }