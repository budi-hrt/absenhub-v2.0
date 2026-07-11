<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasaKontrak extends Model
{
    protected $guarded = [];

    /**
     * RELASI KE TABEL KONTRAKS
     * Satu masa kontrak bisa dipakai di banyak kontrak
     */
    public function kontraks()
    {
        return $this->hasMany(Kontrak::class, 'masa_kontrak_id', 'id');
    }
}
