<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    //
    protected $table = 'usertype';
    protected $primaryKey = 'usertype_id';

    public function user()
    {
        return $this->belongsTo('App\User','usertype_id','usertype_id');
    }
}
