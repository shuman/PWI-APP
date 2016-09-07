<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of UserSocialMedial
 *
 * @author PWI
 */
use Illuminate\Database\Eloquent\Model;

class UserSocialMedia extends Model {

    protected $table = 'pwi_users_social';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
