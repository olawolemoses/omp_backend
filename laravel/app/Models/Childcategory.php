<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Childcategory extends Model
{
  
  protected $fillable = ['subcategory_id','name','slug'];
  public $timestamps = false;

  public function subcategory() {
    return $this->belongsTo('App\Subcategory');
  }

  public function products() {
    return $this->hasMany('App\Product');
  }
  
  public function setSlugAttribute($value) {
    $this->attributes['slug'] = str_replace(' ', '-', $value);
  }

}