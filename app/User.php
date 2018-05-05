<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $table = 'users';

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_fname', 
        'user_lname',
        'user_tel',
        'user_sex',
        'user_address',
        'user_city',
        'user_state',
        'user_country',
        'user_description',
        'email', 
        'password',
        'user_verifications_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function diaries() {
        return $this->hasMany('App\Diary');
    }

    public function houses() {
        return $this->hasMany('App\House');
    }

    public function rentals() {
        return $this->hasMany('App\Rental');
    }

    public function reviews() {
        return $this->hasMany('App\Review');
    }

    public function user_verifications() {
        return $this->belongsTo('App\UserVerification');
    }
}
