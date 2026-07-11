<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nonaktif extends Model
{
    protected $fillable = ['karyawan_id', 'tanggal_aktif', 'tanggal_nonaktif', 'alasan'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
