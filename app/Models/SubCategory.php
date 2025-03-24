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

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function manuals()
    {
        return $this->hasMany(Manual::class);
    }
}
