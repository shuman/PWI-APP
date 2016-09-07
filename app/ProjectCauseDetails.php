<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectCauseDetails extends Model
{
    //
    protected $table = "pwi_project_cause_details";

    protected $primaryKey = "project_cause_id";

    protected $fillable = ["org_id", "project_id", "project_cause_type", "project_cause_item_id", "project_cause_status"];
}
