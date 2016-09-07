<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    //
    protected $table = "pwi_categories";
    
    protected $primary_key = "category_id";
    
    public function category_count( ){
        return $this->belongsToMany("App\Products", "pwi_product_categories", "product_category_id", "product_id")
                    ->where("pwi_products.product_status", "=", "active")
                    ->select( "COUNT(pwi_category.category_id) AS CNT", "category_name", "category_id")
                    ->groupBy("category_name");
    }
}
