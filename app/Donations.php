<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Donations extends Model
{
    //
    protected $table = "pwi_donations";
    
    protected $primaryKey = "donation_id";
    
    protected $fillable = ['user_id', 'item_id', 'item_type', 'donation_amount', 'billing_full_name', 'billing_address_line1', 'billing_address_line2', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'donated_date', 'payment_gateway', 'donation_status', 'transaction_id'];
}
