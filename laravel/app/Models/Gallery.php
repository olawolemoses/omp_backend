<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class Gallery extends Model
  {
      protected $fillable = ['product_id','photo'];
      public $timestamps = false;
  }