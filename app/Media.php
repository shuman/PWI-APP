<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of Media
 *
 * @author Shuvo
 */
use Illuminate\Database\Eloquent\Model;

class Media extends Model {

    protected $table = 'pwi_social_media';
    protected $primaryKey = 'social_media_id';
    public $timestamps = false;

}
