<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{    
    protected $guarded=[];
    public $timestamps=false;
    public function user(){
        return $this->hasOne('App\User');
    }

    public function branches(){
        return $this->hasMany('App\Branch');
    }
}
