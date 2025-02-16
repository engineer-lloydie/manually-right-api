<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualFile extends Model
{
    protected $fillable = [
        'manual_id',
        'title',
        'filename',
        'status'
    ];
}
