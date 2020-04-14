<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class SubCategory extends Model 
  {
    protected $fillable = ['category_id','name','slug','status'];
    public $timestamps = false;

    protected $table = 'subcategories';

    public function childs() {
    	return $this->hasMany('App\Models\Childcategory',  "subcategory_id" )->where('status','=',1);
    }

    public function category() {
    	return $this->belongsTo('App\Models\Category');
    }
    
    public function products() {
      return $this->hasMany('App\Models\Product');
    }
    
    public function setSlugAttribute($value) {
      $this->attributes['slug'] = str_replace(' ', '-', $value);
    }

  }