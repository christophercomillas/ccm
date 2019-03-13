<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckHistory extends Model
{
    //
    protected $table = 'checkhistory';
    protected $primaryKey = 'checkhistory_id';
    public $timestamps = false;

    // public function checkreceived()
    // {
    //     return $this->belongsTo('App\CheckReceived','checksreceivingtransaction_id','checksreceivingtransaction_id');
    // }

    public function customer()
    {
        return $this->belongsTo('App\Customer','customer_id','customer_id');
    }

    public function bank()
    {
        return $this->belongsTo('App\Bank','bank_id','bank_id');
    }    

    public function checktaggingitem()
    {
        return $this->belongsTo('App\CheckTaggingItem','checktaggingitems_id','checktaggingitems_id');
    }
}
