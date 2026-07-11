<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $guarded = [];

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'jabatan_id', 'id');
    }

    public function penandatangans()
    {
        return $this->hasMany(Penandatangan::class, 'jabatan_id', 'id');
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'jabatan_id', 'id');
    }
}
