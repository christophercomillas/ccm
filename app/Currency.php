<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //
    protected $table = 'currency';
    protected $primaryKey = 'currency_id';

    public function check()
    {        
        return $this->hasMany('App\Check','currency_id','currency_id');
    }   
}
