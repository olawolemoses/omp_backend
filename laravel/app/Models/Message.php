<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Message extends Model
  {
    protected $fillable =  ['conversation_id', 'message','subject', 'sent_user', 'recieved_user'];

    public function conersation() {
      return $this->belongTo('App\Models\conversation');
    }

  }
  