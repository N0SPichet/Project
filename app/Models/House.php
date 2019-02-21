<?php

namespace App\Models;

use App\Models\District;
use App\Models\Food;
use App\Models\Himage;
use App\Models\Houseamenity;
use App\Models\Housedetail;
use App\Models\Houserule;
use App\Models\Housespace;
use App\Models\Housetype;
use App\Models\Province;
use App\Models\SubDistrict;
use App\User;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $table = 'houses';

    public function apartmentprices() {
        return $this->belongsTo('App\Apartmentprice');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function users() {
    	return $this->belongsTo(User::class);
    }

    public function rentals() {
        return $this->belongsTo('App\Rental');
    }

    public function guestarrives() {
        return $this->belongsTo('App\Guestarrive');
    }

    public function images() {
        return $this->hasMany(Himage::class);
    }

    public function houseamenities() {
        return $this->belongsToMany(Houseamenity::class);
    }

    public function housedetails() {
        return $this->belongsToMany(Housedetail::class);
    }

    public function houserules() {
        return $this->belongsToMany(Houserule::class);
    }

    public function housespaces() {
        return $this->belongsToMany(Housespace::class);
    }

    public function housetypes() {
        return $this->belongsTo(Housetype::class);
    }

    public function foods() {
        return  $this->belongsTo(Food::class);
    }

    public function houseprices() {
        return $this->belongsTo('App\Houseprice');
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function reviews() {
        return $this->hasMany('App\Review');
    }

    public function sub_district()
    {
        return $this->belongsTo(SubDistrict::class);
    }
}
