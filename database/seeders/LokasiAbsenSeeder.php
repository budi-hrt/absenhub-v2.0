<?php

namespace Database\Seeders;

use App\Models\LokasiAbsen;
use Illuminate\Database\Seeder;

class LokasiAbsenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LokasiAbsen::updateOrCreate(
            ['id' => 1],
            [
                'nama_lokasi' => 'Kantor Pusat (Monas)',
                'latitude' => -6.175392,
                'longitude' => 106.827153,
                'radius' => 100, // 100 meter
                'is_active' => true,
            ],
        );
    }
}
