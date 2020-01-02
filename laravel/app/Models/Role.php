<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model{
    protected $fillable = ['name', 'permission'];

    public $timestamps = false;

    protected $table = 'roles';
    public function admins()
    {
        return $this->hasMany('App\Admin');
    }


}