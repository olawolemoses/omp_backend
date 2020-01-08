<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Message extends Model
  {
    protected $fillable =  ['consation_id', 'message', 'sent_user', 'received_user'];

    public function conersation() {
      return $this->belongTo('App\Models\conversation');
    }

  }
  