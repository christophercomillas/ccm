<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salesman extends Model
{
    //
    protected $table = 'salesman';
    protected $primaryKey = 'salesman_id';

    public function user()
    {
        return $this->belongsTo('App\User','id','id');
    }
}
