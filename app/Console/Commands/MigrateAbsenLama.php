<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateAbsenLama extends Command
{
    protected $signature = 'app:migrate-absen';

    protected $description = 'Menarik ratusan ribu data absensi dari CI3 ke Laravel dengan sistem pemetaan PIN Mesin';

    public function handle()
    {
        $this->info('Memulai persiapan data Karyawan untuk pemetaan...');

        // 1. Tarik semua data Karyawan Baru, lalu jadikan 'pin_mesin' sebagai kunci array
        // Hasilnya: [pin_mesin => id_karyawan_database] (contoh: [151 => 189])
        $karyawanBaru = DB::table('karyawans')
            ->whereNotNull('pin_mesin')
            ->pluck('id', 'pin_mesin')
            ->toArray();

        $this->info('Mulai menarik dan memindahkan data Absensi (Diproses per 1.000 baris)...');

        $totalBerhasil = 0;

        // 2. Tarik data dari database CI3 lama secara bertahap (Chunk)
        // SESUAIKAN: ganti 'tabel_absen_lama' dengan nama tabel absen di CI3 Anda
        DB::connection('mysql_lama')
            ->table('absen')
            ->orderBy('tanggal_absen') // SESUAIKAN: kolom tanggal absen di CI3
            ->chunk(1000, function ($dataAbsenLama) use ($karyawanBaru, &$totalBerhasil) {

                $dataInsert = [];

                foreach ($dataAbsenLama as $lama) {
                    // SESUAIKAN: kolom pin mesin di tabel absen lama Anda (sebelumnya Anda sebut id_absen)
                    $pinMesin = $lama->id_absensi;

                    // Cek apakah PIN mesin dari log absen ini ada pemiliknya di tabel karyawan
                    if (isset($karyawanBaru[$pinMesin])) {

                        // Terjemahkan ID Keterangan (0, 1, 2, 3) menjadi Teks
                        // SESUAIKAN: nama kolom keterangan di CI3 (misal: id_ket)
                        $teksKeterangan = match ((int) ($lama->id_ket ?? 0)) {
                            1 => 'Sakit',
                            2 => 'Dinas Luar',
                            3 => 'Cuti',
                            4 => 'Izin',
                            5 => 'Off',
                            6 => 'Pulang Cepat/Lambat Datang',
                            7 => 'Tidak Absen',
                            8 => 'Libur',
                            9 => 'Lainnya',
                            10 => 'Alpa',
                            default => 'Hadir', // 0 atau kosong dianggap Hadir
                        };

                        $dataInsert[] = [
                            // Rahasianya ada di sini: Mengubah PIN mesin jadi ID relasi yang benar!
                            'karyawan_id' => $karyawanBaru[$pinMesin],

                            // SESUAIKAN DENGAN NAMA KOLOM CI3 ANDA
                            'tanggal_absen' => $lama->tanggal_absen,
                            'scan_in' => $lama->scan_in ?? null,
                            'scan_out' => $lama->scan_out ?? null,
                            'keterangan' => $teksKeterangan,

                            // Jika dulu belum ada fitur GPS/Foto, biarkan null
                            'lat_in' => null,
                            'long_in' => null,
                            'lat_out' => null,
                            'long_out' => null,
                            'foto_in' => null,
                            'foto_out' => null,

                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // 3. Masukkan 1.000 data sekaligus ke tabel baru (Bulk Insert)
                if (! empty($dataInsert)) {
                    DB::table('absens')->insert($dataInsert);
                    $totalBerhasil += count($dataInsert);
                    $this->info("Berhasil memindahkan $totalBerhasil baris...");
                }
            });

        $this->info("Selesai! Total $totalBerhasil data Absensi sukses dipindahkan dengan relasi yang bersih.");
    }
}
