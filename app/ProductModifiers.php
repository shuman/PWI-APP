<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductModifiers extends Model
{
    //
     protected $table = "pwi_product_modifiers";
    
    protected $primaryKey = "product_modifier_id";
    
    protected $fillable = ["product_id", "product_modifier_title", "product_modifier_status"];

    public function products( ){
        return $this->belongsTo("App\Products");
    }
}
