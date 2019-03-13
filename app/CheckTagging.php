<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckTagging extends Model
{
    //
    protected $table = 'checktagging_hdr';
    protected $primaryKey = 'checktagginghdr_id';

    public function user()
    {
        return $this->belongsTo('App\User','id','id');
    }

    public function checktaggingitem()
    {
        return $this->hasMany('App\CheckTaggingItem','checktagginghdr_id','checktagginghdr_id');
    }
}
