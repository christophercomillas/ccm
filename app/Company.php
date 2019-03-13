<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $table = 'company';
    protected $primaryKey = 'company_id';

    public function user()
    {
        return $this->belongsTo('App\User','company_id','company_id');
    }

    
}
