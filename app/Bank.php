<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    //
    protected $table = 'banks';
    protected $primaryKey = 'bank_id';

    public function user()
    {
        return $this->belongsTo('App\User','id','id');
    }
    public function check()
    {        
        return $this->hasMany('App\Check','bank_id','bank_id');
    }    
}
