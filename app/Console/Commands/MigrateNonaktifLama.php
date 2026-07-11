<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateNonaktifLama extends Command
{
    protected $signature = 'app:migrate-nonaktif';

    protected $description = 'Migrasi data riwayat Karyawan Nonaktif dari CI3 ke Laravel';

    public function handle()
    {
        $this->info('Memulai migrasi data Karyawan Nonaktif...');

        // 1. Tarik data nonaktif dari database CI3 lama
        // SESUAIKAN: ganti 'tabel_nonaktif_lama' dengan nama tabel asli di CI3 Anda
        $nonaktifLama = DB::connection('mysql_lama')->table('tb_nonaktif')->get();

        // 2. Ambil ID Karyawan yang valid untuk mencegah error jika ada data yatim piatu
        $validKaryawanIds = DB::table('karyawans')->pluck('id')->toArray();
        $jumlahData = 0;

        foreach ($nonaktifLama as $lama) {

            // Pastikan id_karyawan benar-benar ada di tabel karyawans baru
            // SESUAIKAN: ganti $lama->id_karyawan dengan nama kolom id karyawan di tabel nonaktif CI3 Anda
            if (in_array($lama->id_karyawan, $validKaryawanIds)) {

                DB::table('nonaktifs')->insert([
                    // SESUAIKAN: nama kolom di bawah ini dengan nama kolom di database CI3 Anda
                    'karyawan_id' => $lama->id_karyawan,
                    'tanggal_aktif' => ($lama->tgl_aktif === '0000-00-00' || empty($lama->tgl_aktif)) ? null : $lama->tgl_aktif,
                    'tanggal_nonaktif' => ($lama->tgl_nonaktif === '0000-00-00' || empty($lama->tgl_nonaktif)) ? null : $lama->tgl_nonaktif,
                    'alasan' => empty(trim($lama->alasan)) ? 'Tanpa Keterangan' : trim($lama->alasan),

                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $jumlahData++;
            }
        }

        $this->info("MISSION ACCOMPLISHED! Berhasil memindahkan $jumlahData data Karyawan Nonaktif.");
    }
}
