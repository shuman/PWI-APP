<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryData extends Model
{
    //
    protected $table = "pwi_country_data";
    
    public function country( ){
        return $this->belongsTo('App\Country', 'country_id');
    }
    
    
}
