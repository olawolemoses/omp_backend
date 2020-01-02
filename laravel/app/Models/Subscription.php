<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class Subscription extends Model
  {
      protected $fillable = ['title','currency','currency_code','price','days','allowed_products','details'];
      public $timestamps = false;
  }