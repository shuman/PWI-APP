<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    //
    protected $table = "pwi_product_images";
    
    protected $primaryKey = "product_image_id";

    protected $fillable = ["product_id", "file_id", "product_image_status"];
    
    public function products( ){
        return $this->belongsTo("App\Products");
    }
}
