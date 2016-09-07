<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMaster extends Model {

    protected $table = "pwi_order_master";
    protected $primaryKey = "order_id";
    protected $fillable = ["user_id", "billing_full_name", "billing_email", "billing_address_line1", "billing_address_line2", "billing_city", "billing_state", "billing_zip", "billing_country", "shipping_full_name", "shipping_email", "shipping_address_line1", "shipping_address_line2", "shipping_city", "shipping_state", "shipping_zip", "shipping_country", "product_count", "order_date", "transaction_id", "payment_gateway", "order_status", "order_item_total", "order_shipping_cost", "order_tax", "order_cost"];

}
