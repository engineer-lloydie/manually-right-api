<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manual extends Model
{
    protected $fillable = [
        'sub_category_id',
        'title',
        'description',
        'price',
        'url_slug',
        'status'
    ];
}
