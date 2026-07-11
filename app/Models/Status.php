<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $guarded = [];

    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'status_id');
    }
}
