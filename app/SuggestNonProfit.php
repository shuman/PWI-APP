<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuggestNonProfit extends Model
{
    //
    protected $table = "pwi_suggested_nonprofits";
    
    protected $fillable = ["nonprofit_website", "nonprofit_poc", "suggestors_name", "suggestors_email"];
}
