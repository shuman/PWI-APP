<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategories extends Model
{
    //
    protected $table = "pwi_categories";
    
    protected $primaryKey = "category_id";
}
