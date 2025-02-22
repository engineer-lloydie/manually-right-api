<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'cart_id',
        'order_master_id',
        'item_number',
        'subtotal',
        'download_count',
        'status'
    ];
}
