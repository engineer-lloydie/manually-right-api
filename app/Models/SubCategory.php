<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'main_category_id',
        'name',
        'description',
        'url_slug',
        'thumbnail',
        'status'
    ];
}
