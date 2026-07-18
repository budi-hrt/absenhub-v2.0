<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanAbsen extends Model
{
    protected $guarded = [];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'is_active' => 'boolean',
    ];
}
