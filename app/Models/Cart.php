<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'manual_id',
        'price',
        'quantity',
        'status'
    ];

    public function manual() {
        return $this->belongsTo(Manual::class, 'manual_id')
            ->with(['thumbnails' => function ($query) {
                $query->first();
            }]);
    }
}
