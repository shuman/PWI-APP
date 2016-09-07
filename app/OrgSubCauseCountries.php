<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrgSubCauseCountries extends Model
{
    //
    protected $table = "pwi_org_subcause_countries";
    
    protected $primaryKey = "org_sc__id";

    protected $fillable = ["org_id", "cause_id", "org_cause_id", "org_sc_type", "org_sc_item_id", "org_sc_status"];
    
    public function country( ){
        return $this->belongsTo("App\Country", "org_sc_item_id", "country_id");
    }
    
    public function organization( ){
        return $this->belongsTo("App\Organizations", "org_id", "org_id");
    }
}
