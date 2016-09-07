<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model {

    protected $table = "pwi_follow";
    protected $primaryKey = "follow_id";
    protected $fillable = ["follow_type", "follow_type_id", "follow_user_id", "follow_started_from", "follow_status"];

}
