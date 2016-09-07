<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCauses extends Model
{
    //
    protected $table = "pwi_product_causes";

    protected $primaryKey = "product_cause_id";

    protected $fillable = ["product_id", "product_cause_type", "product_cause_item_id", "product_cause_status"];
}
