<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    /**
     * RELASI KE TABEL KARYAWAN
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id');
    }

    /**
     * RELASI KE TABEL MASA KONTRAK
     */
    public function masaKontrak()
    {
        return $this->belongsTo(MasaKontrak::class, 'masa_kontrak_id', 'id');
    }

    /**
     * RELASI KE TABEL PENANDATANGAN
     */
    public function penandatangan()
    {
        return $this->belongsTo(Penandatangan::class, 'penandatangan_id', 'id');
    }
}
