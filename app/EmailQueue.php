<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailQueue extends Model
{
    //
    protected $table = "pwi_email_queue";

    protected $fillable = ['type', 'type_id', 'sent', 'date_sent'];
}
