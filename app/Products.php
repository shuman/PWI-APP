<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    //
    protected $table = "pwi_products";
    
    protected $primaryKey = "product_id";

    protected $fillable = ["org_id", "product_type", "product_name", "product_alias", "product_sku", "product_regular_price", "product_sales_price", "product_display_price", "product_short_desc", "product_full_desc", "product_image_id", "product_quantity", "product_shipping_fee", "product_shipping_time", "product_weight", "product_width", "product_length", "product_height", "product_status", "product_viewcount", "product_featured", "featured_order", "product_created_date", "fedex_enabled", "usps_enabled", "ups_enabled"];
    
    public function image( ){
        return $this->hasOne("App\Files", "file_id", "product_image_id");
    }
    
    public function rating( ){
        return $this->hasMany("App\Rating", "comment_item_id", "product_id");
    }
    
    public function images( ){
        return $this->belongsToMany("App\Files", "pwi_product_images", "product_id", "file_id");
    }
    
    public function categories( ){
        return $this->belongsToMany("App\Categories", "pwi_product_categories", "product_id", "category_id");
    }
    
    public function causes( ){
        return $this->belongsToMany("App\Causes", "pwi_product_causes", "product_id", "product_cause_item_id")
                    ->where("product_cause_type", "=", "cause")
                    ->where("product_cause_status","=", "active");
    }
    
    public function impacts( ){
        return $this->belongsToMany("App\Country", "pwi_product_causes", "product_id", "product_cause_item_id")
                    ->where("product_cause_type", "=", "country")
                    ->where("product_cause_status","=", "active");
    }
    
    public function modifiers( ){
        return $this->hasMany("App\ProductModifiers", "product_id", "product_id");
    }
}
