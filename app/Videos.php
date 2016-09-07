<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Videos extends Model {

    protected $table = "pwi_org_videos";
    protected $primaryKey = "org_video_id";
    protected $fillable = ["org_id", "video_url", "video_id", "org_video_status", "createdatetime", "sequence"];

    public function organizations() {
        return $this->belongsTo("App\Organizations");
    }

}
