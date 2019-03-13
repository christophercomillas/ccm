<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name','password','username',
    // ];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function company()
    {
        return $this->hasOne('App\Company','company_id','company_id');
    }

    public function businessunit()
    {
        return $this->hasOne('App\BusinessUnit','businessunit_id','businessunit_id');
    }

    public function department()
    {
        return $this->hasOne('App\Department','department_id','department_id');
    }

    public function usertype()
    {
        return $this->hasOne('App\UserType','usertype_id','usertype_id');
    }

    public function salesman()
    {
        return $this->hasMany('App\Salesman','id','id');
    }

    public function bank()
    {
        return $this->hasMany('App\Bank','id','id');
    }

    public function customer()
    {
        return $this->hasMany('App\Customer','id','id');
    }

    public function checktagging()
    {
        return $this->hasMany('App\CheckTagging','id','id');
    }
}
