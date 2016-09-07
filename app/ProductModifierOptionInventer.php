<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductModifierOptionInventer extends Model
{
    
    protected $table = "pwi_product_modifier_option_inventers";

    protected $primaryKey = "pm_option_inventer_id";

    protected $fillable = ["product_id", "pm_option_inventory", "pm_option_inventory_names", "pm_option_price", "pm_option_quantity", "pm_option_shippingfee", "pm_option_shipping_time", "pm_option_weight", "pm_option_length", "pm_option_width", "pm_option_width", "pm_option_height", "pm_option_parent_shipping"];
}
