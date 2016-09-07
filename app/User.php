<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\UserProvider;

class User extends Authenticatable {
    /* The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = "pwi_users";

    /* The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = "user_id";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getAuthPassword() {
        return \Hash::make($this->password);
    }

    public function getEmail() {
        return $this->user_email;
    }

    public function getAddresses() {
        return $this->hasMany("App\UserAddress", "user_addr_user_id")
                        ->leftJoin("pwi_state as STATE", "STATE.state_id", "=", "pwi_user_address.user_addr_state")
                        ->leftJoin("pwi_country as CTRY", "CTRY.country_id", "=", "pwi_user_address.user_addr_country_code")
                        ->select("user_addr_id as id", "user_addr_address_type as type", "user_addr_line1 as addrLine1", "user_addr_line2 as addrLine2", "user_addr_city as city", "user_addr_state as stateId", "STATE.state_code as state", "user_addr_country_code as countryId", "CTRY.country_iso_code as country", "user_addr_zip as zip");
    }

    public function url_exists($url = '') {
        $file_headers = @get_headers($url);
        if ($file_headers[0] == 'HTTP/1.0 404 Not Found') {
            $file_exists = false;
        } else {
            $file_exists = true;
        }
        return $file_exists;
    }

}
