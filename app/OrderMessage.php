<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of OrderMessage
 *
 * @author PWI
 */
use Illuminate\Database\Eloquent\Model;

class OrderMessage extends Model {

    protected $table = 'pwi_order_messages';
    protected $primaryKey = 'message_id';
    public $timestamps = false;

}
