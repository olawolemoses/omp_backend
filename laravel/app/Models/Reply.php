<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Reply extends Model 
  {
    protected $fillable = ['comment_id', 'user_id','text'];

    public function user() {
    	return $this->belongsTo('App\User');
    }

    public function comment() {
    	return $this->belongsTo('App\Comment');
    }

    public function subreplies() { 
      return $this->hasMany('App\SubReply');
    }

  }
