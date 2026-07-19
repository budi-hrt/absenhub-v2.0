<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flags = [
            [
                'key' => 'manual_checkin_edit',
                'name' => 'Edit Manual Check In',
                'description' => 'Izinkan admin mengedit jam check in secara manual di halaman Kelola Absensi',
                'group' => 'absensi',
                'is_enabled' => true,
            ],
            [
                'key' => 'manual_checkout_edit',
                'name' => 'Edit Manual Check Out',
                'description' => 'Izinkan admin mengedit jam check out secara manual di halaman Kelola Absensi',
                'group' => 'absensi',
                'is_enabled' => true,
            ],
            [
                'key' => 'import_absen',
                'name' => 'Import Absensi',
                'description' => 'Tampilkan tombol import absensi dari file CSV/Excel',
                'group' => 'absensi',
                'is_enabled' => true,
            ],
            [
                'key' => 'collective_keterangan',
                'name' => 'Keterangan Kolektif',
                'description' => 'Izinkan perubahan keterangan absensi secara kolektif (batch)',
                'group' => 'absensi',
                'is_enabled' => true,
            ],
            [
                'key' => 'face_recognition',
                'name' => 'Face Recognition',
                'description' => 'Aktifkan fitur absensi menggunakan pengenalan wajah',
                'group' => 'absensi',
                'is_enabled' => true,
            ],
            [
                'key' => 'gps_tracking',
                'name' => 'GPS Tracking',
                'description' => 'Rekam lokasi GPS saat karyawan melakukan absensi',
                'group' => 'absensi',
                'is_enabled' => true,
            ],
            [
                'key' => 'karyawan_export',
                'name' => 'Export Data Karyawan',
                'description' => 'Tampilkan tombol export data karyawan ke Excel/PDF',
                'group' => 'karyawan',
                'is_enabled' => true,
            ],
            [
                'key' => 'laporan_pdf',
                'name' => 'Export Laporan PDF',
                'description' => 'Izinkan download laporan absensi dalam format PDF',
                'group' => 'laporan',
                'is_enabled' => true,
            ],
        ];

        foreach ($flags as $flag) {
            FeatureFlag::updateOrCreate(['key' => $flag['key']], $flag);
        }
    }
}
