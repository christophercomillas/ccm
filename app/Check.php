<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Check extends Model
{
    //
    use SoftDeletes;
    protected $table = 'checks';
    protected $primaryKey = 'checks_id';
    public $timestamps = false;

    protected $dates = ['deleted_at'];

    public function checkreceived()
    {
        return $this->belongsTo('App\CheckReceived','checksreceivingtransaction_id','checksreceivingtransaction_id');
    }

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
        return $this->hasMany('App\CheckTaggingItem','checktagginghdr_id','checktagginghdr_id');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency','currency_id','currency_id');
    } 
}
