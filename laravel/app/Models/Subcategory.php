<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class SubCategory extends Model 
  {
    protected $fillable = ['category_id','name','slug'];
    public $timestamps = false;

    protected $table = 'subcategories';

    public function childs() {
    	return $this->hasMany('App\Childcategory')->where('status','=',1);
    }

    public function category() {
    	return $this->belongsTo('App\Category');
    }
    
    public function products() {
      return $this->hasMany('App\Product');
    }
    
    public function setSlugAttribute($value) {
      $this->attributes['slug'] = str_replace(' ', '-', $value);
    }

  }