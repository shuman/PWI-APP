<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model {

    //
    protected $table = "pwi_user_address";
    protected $primaryKey = "user_addr_id";
    public $timestamps = false;
    protected $fillable = ["user_addr_user_id", "user_addr_address_type", "user_addr_line1", "user_addr_line2", "user_addr_city", "user_addr_country_code", "user_addr_state", "user_addr_zip", "user_addr_isDefault", "user_addr_status", "user_addr_fname", "user_addr_lname"];

    public function users() {
        $this->belongsTo("App\User");
    }

}
