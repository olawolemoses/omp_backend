<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model 
{
  protected $fillable = ['name','slug','photo','is_featured','image'];
  public $timestamps = false;

  public function subs() {
    return $this->hasMany('App\Subcategory')->where('status','=',1);
  }

  public function products() {
    return $this->hasMany('App\Product');
  }
  
  public function setSlugAttribute($value) {
    $this->attributes['slug'] = str_replace(' ', '-', $value);
  }

} 