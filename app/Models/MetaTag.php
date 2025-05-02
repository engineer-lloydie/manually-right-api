<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MetaTag extends Model
{
    protected $fillable = [
        'metaable_id',
        'metaable_type',
        'title',
        'description',
        'keywords',
    ];

    public function metaable(): MorphTo
    {
        return $this->morphTo();
    }
}
