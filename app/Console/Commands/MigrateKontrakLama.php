<?php

namespace App\Console\Commands;

use App\Models\Jabatan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // Kita butuh ini untuk otomatisasi jabatan penandatangan

class MigrateKontrakLama extends Command
{
    protected $signature = 'app:migrate-kontrak';

    protected $description = 'Migrasi data Kontrak beserta master Penandatangan dan Masa Kontrak';

    public function handle()
    {
        $this->info('1. Memulai migrasi master Masa Kontrak...');

        // Tarik data status kontrak lama
        $statusLama = DB::connection('mysql_lama')->table('status_kontrak')->get();
        foreach ($statusLama as $lama) {
            DB::table('masa_kontraks')->insertOrIgnore([
                'id' => $lama->id, // Pertahankan ID asli agar relasi tidak putus
                'status_kontrak' => $lama->status_kontrak,
                'is_active' => $lama->is_active ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->info('   Berhasil memindahkan Masa Kontrak!');

        $this->info('2. Memulai migrasi master Penandatangan...');

        // Tarik data penandatangan lama
        $penandatanganLama = DB::connection('mysql_lama')->table('tb_penandatangan')->get();
        foreach ($penandatanganLama as $lama) {

            // Ubah teks jabatan menjadi ID (Normalisasi on-the-fly)
            $teksJabatan = trim($lama->jabatan ?? 'Tanpa Jabatan');
            $jabatan = Jabatan::firstOrCreate(['nama_jabatan' => $teksJabatan]);

            DB::table('penandatangans')->insertOrIgnore([
                'id' => $lama->id, // Pertahankan ID asli
                'nama_penandatangan' => $lama->nama_penandatangan,
                'alamat' => $lama->alamat ?? '-',
                'jabatan_id' => $jabatan->id, // Hasil normalisasi
                'is_active' => $lama->is_active ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->info('   Berhasil memindahkan Penandatangan!');

        $this->info('3. Memulai migrasi data Transaksi Kontrak...');

        // Tarik data kontrak lama
        $kontrakLama = DB::connection('mysql_lama')->table('kontrak')->get();

        // Ambil ID Karyawan yang valid untuk mencegah error jika ada kontrak dari karyawan yang sudah terhapus
        $validKaryawanIds = DB::table('karyawans')->pluck('id')->toArray();
        $jumlahKontrak = 0;

        foreach ($kontrakLama as $lama) {

            // Pastikan id_karyawan ada di tabel karyawans baru
            if (in_array($lama->id_karyawan, $validKaryawanIds)) {

                DB::table('kontraks')->insert([
                    'id' => $lama->id_kontrak_baru, // Pakai ID asli dari CI3

                    'nomor' => empty(trim($lama->nomor)) ? null : trim($lama->nomor),

                    // Filter tanggal fiktif (0000-00-00) menjadi null seperti sebelumnya
                    'tanggal_surat' => ($lama->tanggal_surat === '0000-00-00' || empty($lama->tanggal_surat)) ? null : $lama->tanggal_surat,
                    'tanggal_mulai' => ($lama->tanggal_mulai === '0000-00-00' || empty($lama->tanggal_mulai)) ? null : $lama->tanggal_mulai,
                    'tanggal_akhir' => ($lama->tanggal_akhir === '0000-00-00' || empty($lama->tanggal_akhir)) ? null : $lama->tanggal_akhir,

                    // Relasi ID di-mapping langsung
                    'penandatangan_id' => $lama->id_penandatangan,
                    'karyawan_id' => $lama->id_karyawan,
                    'masa_kontrak_id' => $lama->id_status,

                    // Angka nominal
                    'gaji' => $lama->gaji ?? 0,
                    'tunjangan' => $lama->tunjangan ?? 0,
                    'um_dalamkota' => $lama->um_dalamkota ?? 0,
                    'um_luarkota' => $lama->um_luarkota ?? 0,

                    'doc_ttd' => $lama->doc_ttd ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $jumlahKontrak++;
            }
        }

        $this->info("Selesai! Berhasil memindahkan $jumlahKontrak data Kontrak tanpa memutus relasinya.");
    }
}
