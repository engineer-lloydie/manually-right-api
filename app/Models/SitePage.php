<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class SitePage extends Model
{
    protected $fillable = [
        'name',
        'url_slug',
        'status',
    ];

    public function metaTags(): MorphOne
    {
        return $this->morphOne(MetaTag::class, 'metaable');
    }
}
