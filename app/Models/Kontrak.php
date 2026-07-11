<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    protected $guarded = [];

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
}
