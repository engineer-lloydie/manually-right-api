<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualThumbnail extends Model
{
    protected $fillable = [
        'manual_id',
        'filename',
        'status'
    ];
}
