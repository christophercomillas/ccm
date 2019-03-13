<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    public function check()
    {        
        return $this->hasMany('App\Check','customer_id','customer_id');
    }    

    public function user()
    {
        return $this->belongsTo('App\User','id','id');
    }

}
