<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductShipMethods extends Model
{
    //
    protected $table = "pwi_product_shipmethods";

    protected $primaryKey = "shipmethod_id";

    protected $fillable = ["product_id", "ship_method", "shipmethod_status"];
}
