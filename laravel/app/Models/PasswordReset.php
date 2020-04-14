<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model 
{
  protected $fillable = ['email','key','expDate'];
  
  protected $table = "password_reset_temp";
  
  public $timestamps = false;

  public function subs() {
    return $this->hasMany('App\Models\Subcategory')->where('status','=',1);
  }

  public function products() {
    return $this->hasMany('App\Models\Product');
  }
  
  public function setSlugAttribute($value) {
    $this->attributes['slug'] = str_replace(' ', '-', $value);
  }

} 