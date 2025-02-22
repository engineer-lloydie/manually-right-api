<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestOrder extends Model
{
    protected $fillable = [
        'guest_id',
        'order_master_id',
        'email_address',
        'first_name',
        'last_name'
    ];
}
