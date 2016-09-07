<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model {

    protected $table = "pwi_order_details";
    protected $primaryKey = "order_detail_id";
    protected $fillable = ["order_id", "product_id", "product_name", "product_sku", "parent_modifier", "modifier_id", "modifier_name", "quantity", "status", "product_price", "product_shipping", "org_id"];

}
