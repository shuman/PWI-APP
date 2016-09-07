<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrgPhotos extends Model
{
    //
    protected $table = "pwi_org_photos";
    protected $primaryKey = "org_photo_id";
    protected $fillable = ["org_id", "file_id", "org_photo_status", "createdatetime", "sequence"];
}
