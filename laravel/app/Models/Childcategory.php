<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Childcategory extends Model
{
  protected $table = 'childcategories';


  protected $fillable = ['subcategory_id','name','slug', 'status'];
  public $timestamps = false;

  public function subcategory() {
    return $this->belongsTo('App\Models\Subcategory', 'subcategory_id', 'id');
  }

  public function products() {
    return $this->hasMany('App\Models\Product');
  }
  
  public function setSlugAttribute($value) {
    $this->attributes['slug'] = str_replace(' ', '-', $value);
  }

}