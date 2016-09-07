<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectMaster extends Model
{
    //
    protected $table = "pwi_project_donation_master";
    
    protected $primaryKey = "donation_id";

    protected $fillable = ["user_id", "project_id", "incentive_id", "project_title", "donation_amount", "billing_full_name", "billing_first_name", "billing_last_name", "billing_email", "billing_address_line1", "billing_address_line2", "billing_city", "billing_state", "billing_zip", "billing_country", "shipping_full_name", "shipping_email", "shipping_address_line1", "shipping_address_line2", "shipping_city", "shipping_state", "shipping_zip", "shipping_country", "donated_date", "transaction_id", "payment_gateway", "donation_status"];
}
