<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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

    public function subCategory() {
        return $this->belongsTo(SubCategory::class);
    }

    public function files() {
        return $this->hasMany(ManualFile::class);
    }

    public function thumbnails() {
        return $this->hasMany(ManualThumbnail::class);
    }

    public function metaTags(): MorphOne
    {
        return $this->morphOne(MetaTag::class, 'metaable');
    }
}
