<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiAbsen extends Model
{
    protected $guarded = [];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'integer',
        'is_active' => 'boolean',
    ];
}
