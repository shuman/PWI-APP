<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    //
    protected $table = "pwi_projects";
    
    protected $primaryKey = "project_id";

    protected $fillable = ["project_title", "project_alias", "project_start_date", "project_end_date", "project_icon", "project_video_url", "project_orig_video_url", "project_story", "project_fund_goal", "project_payment_type", "project_status", "project_fund_met", "project_featured", "featured_order", "project_amout_raised", "project_viewcount", "project_create_date"];
    
    public function icon( ){
        return $this->hasOne("App\Files", "file_id", "project_icon");
    }

    public function header( ){
        return $this->hasOne("App\Files", "file_id", "project_header");
    }
    
    public function causes( ){
        return $this->belongsToMany("App\Causes", "pwi_project_cause_details", "project_id", "project_cause_item_id")
                    ->where("project_cause_status","=","active")
                    ->where("project_cause_type", "=", "cause")
                    ->select("pwi_causes.cause_name", "pwi_causes.cause_id");
    }
    
    public function countries( ){
        return $this->belongsToMany("App\Country", "pwi_project_cause_details", "project_id", "project_cause_item_id")
                    ->where("project_cause_status","=","active")
                    ->where("project_cause_type","=","country")
                    ->select("pwi_country.country_name", "pwi_country.country_alias", "pwi_country.country_iso_code", "pwi_country.latitude", "pwi_country.longitude");
    }   
    
    public function incentives( ){
        return $this->hasMany("App\ProjectIncentives", "project_id", "project_id");
    }
    
    public function photos( ){
       // return $this->belongsToMany("App\Files", "pwi_org_photos", "org_id", "file_id");
    }
    
    public function reviews( ){
        return $this->hasMany("App\Rating", "comment_item_id", "project_id")
                    ->where("comment_item", "=", "project");
    }

    public function updates( ){
        return $this->hasMany("App\ProjectUpdates", "project_id", "project_id");
    }
}
