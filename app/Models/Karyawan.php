<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    // Mengizinkan mass-assignment (penting untuk fitur Tambah/Edit Karyawan nanti)
    protected $guarded = [];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * RELASI KE TABEL JABATAN
     * Ini yang membuat $this->jabatan->nama_jabatan bisa berfungsi di Resource
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    /**
     * RELASI KE TABEL STATUS
     * Bonus: Sekalian kita buatkan untuk status (aktif/kontrak/dll)
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    /**
     * RELASI KE TABEL USERS (Akun Login)
     */
    public function user()
    {
        // Karyawan 'milik' User
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * RELASI KE TABEL KONTRAKS
     */
    public function kontraks()
    {
        return $this->hasMany(Kontrak::class, 'karyawan_id', 'id');
    }

    /**
     * RELASI KE TABEL ABSENS
     */
    public function absens()
    {
        return $this->hasMany(Absen::class, 'karyawan_id', 'id');
    }

    /**
     * RELASI KE TABEL NONAKTIFS
     */
    public function nonaktifs()
    {
        return $this->hasMany(Nonaktif::class, 'karyawan_id', 'id');
    }

    /**
     * RELASI KE TABEL PENGAJUAN ABSEN
     */
    public function pengajuanAbsens()
    {
        return $this->hasMany(PengajuanAbsen::class, 'karyawan_id', 'id');
    }
}
