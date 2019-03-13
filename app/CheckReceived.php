<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckReceived extends Model
{
    //
    protected $table = 'checksreceivingtransaction';
    protected $primaryKey = 'checksreceivingtransaction_id';

    public function user()
    {
        return $this->hasMany('App\User','id','id');
    }

    public function salesman()
    {
        return $this->hasMany('App\Salesman','salesman_id','salesman_id');
    }

    public function check()
    {
        return $this->hasMany('App\Check','checksreceivingtransaction_id','checksreceivingtransaction_id');
    }
    
}
