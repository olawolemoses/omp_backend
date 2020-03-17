<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Gallery extends Model
  {
      protected $fillable = ['user_id','title','subtitle','price','user_id','title'];
      public $timestamps = false;
  }