<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectUpdates extends Model
{
    //
    protected $table = "pwi_project_update";

    protected $primaryKey = "project_update_id";

    protected $fillable = ["project_id", "title", "description"];
}
