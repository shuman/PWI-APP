<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectIncentives extends Model
{
    //
    protected $table = "pwi_project_incentives";
    
    protected $primaryKey = "project_incentive_id";

    protected $fillable = ["project_id", "project_incentive_title", "project_incentive_description", "project_incentive_amount", "project_incentive_estdelivery_date", "project_available_incentive_count", "project_donor_shipping_address", "project_incentive_status", "project_incentive_purchasedcount", "project_incentive_created_date"];
    
    public function projects( ){
        return $this->belongsTo("App\Projects");
    }
}
