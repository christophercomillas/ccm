<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckTaggingItem extends Model
{
    //
    protected $table = 'checktagging_items';
    protected $primaryKey = 'checktaggingitems_id';
    public $timestamps = false;

    public function checkTagging()
    {
        return $this->belongsTo('App\CheckTagging','checktagginghdr_id','checktagginghdr_id');
    }

    public function check()
    {
        return $this->belongsTo('App\Check','checks_id','checks_id');
    }
}
