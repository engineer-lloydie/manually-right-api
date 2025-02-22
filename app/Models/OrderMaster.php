<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMaster extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'order_number',
        'transaction_id',
        'total_price',
        'payment_method',
        'purchase_date',
        'payment_status'
    ];

    public function updateOrderNo() {
        $count = 0;
        $countOrder = self::select('id')
            ->whereRaw('DATE(created_at) = DATE("' . date('Y-m-d H:i:s') . '")')
            ->limit(1)
            ->first();

        if (!$countOrder) {
            $count = 1;
        } else {
            $count = (int) ($this->id - $countOrder->id) + 1;
        }

        $uId = sprintf("%04s", $count);
        $orderNumber = date('ymd') . $uId;

        return $this->update(['order_number' => $orderNumber]);
    }

    public function carts() {
        return $this->belongsToMany(Cart::class, 'order_details', 'order_master_id', 'cart_id')->with('manual');
    }

    public function orderDetails() {
        return $this->hasOne(OrderDetail::class);
    }
}
