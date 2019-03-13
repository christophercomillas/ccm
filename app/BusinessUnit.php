<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessUnit extends Model
{
    //
    protected $table = 'businessunit';
    protected $primaryKey = 'businessunit_id';

    public function user()
    {
        return $this->belongsTo('App\User','businessunit_id','businessunit_id');
    }
}
