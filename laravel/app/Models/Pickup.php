<?php 

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class Pickup extends Model
  {
    protected $fillable = ['location'];
    public $timestamps = false;
  }
  