<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
     protected $table = "pwi_comments";
    
     protected $primaryKey = "comment_id";
     
     protected $fillable = ["comment_user_id", "comment_username", "comment_item", "comment_type", "comment_item_id", "comment_text", "comment_rating", "comment_date", "comment_status", "comment_org_type"];
}
