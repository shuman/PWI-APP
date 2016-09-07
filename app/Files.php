<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Files extends Model {

    //
    protected $table = "pwi_files";
    protected $primaryKey = "file_id";
    public $timestamps = false;

    public function country() {
        return $this->belongsTo("App\Country");
    }

}
