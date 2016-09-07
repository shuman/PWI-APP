<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductModifierOptions extends Model
{
    //
     protected $table = "pwi_product_modifier_options";
    
    protected $primaryKey = "pm_option_id";

    protected $fillable = ["product_modifier_id", "product_id", "pm_option_name", "pm_option_price", "pm_option_quantity", "pm_option_shippingfee", "pm_option_shipping_time", "pm_option_weight", "pm_option_length", "pm_option_width", "pm_option_height", "pm_option_parent_shipping"];
    
}
