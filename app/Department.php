<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $table = 'department';
    protected $primaryKey = 'department_id';

    public function user()
    {
        return $this->belongsTo('App\User','department_id','department_id');
    }
}
