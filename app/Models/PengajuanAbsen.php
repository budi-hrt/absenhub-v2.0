<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanAbsen extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'array',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Hitung jumlah hari pengajuan
     */
    public function getJumlahHariAttribute(): int
    {
        return count($this->tanggal ?? []);
    }
}
