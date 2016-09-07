<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model {

    protected $table = "pwi_org_social_media";
    protected $primaryKey = "social_media_id";
    public $timestamps = false;

}
