<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrgCauses extends Model
{
    //

    protected $table = "pwi_org_cause";

    protected $primaryKey = "org_cause_id";

    protected $fillable = ["org_id", "cause_id", "org_cause_description", "org_cause_status"];
}
