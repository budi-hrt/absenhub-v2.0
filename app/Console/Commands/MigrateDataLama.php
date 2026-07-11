<?php

namespace App\Console\Commands;

use App\Models\Jabatan;
use App\Models\Status;
use Illuminate\Console\Command; // Panggil model Jabatan
use Illuminate\Support\Facades\DB;  // Panggil model Status

class MigrateDataLama extends Command
{
    protected $signature = 'app:migrate-data';

    protected $description = 'Menarik data master dari CI3 ke Laravel beserta normalisasi relasinya';

    public function handle()
    {
        $this->info('Memulai penarikan dan normalisasi data Karyawan...');

        // 1. Tarik data dari database CI3 lama
        // SESUAIKAN: ganti 'tabel_karyawan_lama' dengan nama tabel di DB CI3 Anda
        $karyawanLama = DB::connection('mysql_lama')->table('tb_karyawan')->get();

        $jumlahBaris = 0;

        foreach ($karyawanLama as $lama) {

            // --- BAGIAN AJAIB PENCETAK RELASI (NORMALISASI) ---

            // 2A. Otomatisasi Jabatan
            // Ambil nama jabatan dari CI3 (sesuaikan nama kolom 'jabatan_karyawan' dengan punya Anda)
            $teksJabatan = trim($lama->jabatan_karyawan ?? 'Tanpa Jabatan');

            // Laravel akan mencari teks ini di tabel jabatans.
            // Kalau tidak ada, akan dibuatkan otomatis. Kalau ada, langsung ambil ID-nya!
            $jabatan = Jabatan::firstOrCreate(
                ['nama_jabatan' => $teksJabatan]
            );

            // 2. PERBAIKAN: OTOMATISASI STATUS DARI id_status CI3
            $idStatusLama = $lama->id_status ?? 1; // Jika kosong, default ke ID 1

            // Cari berdasarkan ID angka. Kalau belum ada, langsung buatkan di tabel statuses!
            $status = Status::firstOrCreate(
                ['id' => $idStatusLama], // Kunci pencarian berdasarkan ID angka lama
                [
                    // Jika ID-nya 1 kita beri nama 'Karyawan Tetap', selain itu beri nama 'Status X' (bisa diedit nanti)
                    'nama_status' => ($idStatusLama == 1) ? 'Karyawan Tetap' : 'Karyawan Kontrak ' . $idStatusLama,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // --- SELESAI BAGIAN AJAIB ---

            // --- FILTER NIK GANDA ---
            $nikAsli = empty(trim($lama->nik)) ? null : trim($lama->nik);

            if ($nikAsli !== null) {
                // Cek apakah NIK ini sudah masuk duluan ke tabel baru
                $cekDuplikat = DB::table('karyawans')->where('nik', $nikAsli)->exists();
                if ($cekDuplikat) {
                    // Jika duplikat, tambahkan teks -DUP- dan ID aslinya agar jadi unik
                    $nikAsli = $nikAsli . '-DUP-' . $lama->id_karyawan;
                }
            }
            // ------------------------
            // 3. Masukkan ke tabel karyawan Laravel yang baru
            DB::table('karyawans')->insert([
                'id' => $lama->id_karyawan, // ID asli dipertahankan

                // Ini hasil dari fungsi ajaib di atas, teks otomatis jadi ID!
                'jabatan_id' => $jabatan->id,
                'status_id' => $status->id,

                // Data string / angka biasa (termasuk NIK, NPWP, dll) tinggal mapping lurus
                // Jika nik kosong atau hanya berisi spasi, jadikan null
                'nik' => $nikAsli,
                'nama_karyawan' => $lama->nama_karyawan,
                'jk_karyawan' => $lama->jk_karyawan,
                'tempat_lahir' => $lama->tempat_lahir ?? '-',
                'tanggal_lahir' => ($lama->tanggal_lahir === '0000-00-00' || empty($lama->tanggal_lahir)) ? null : $lama->tanggal_lahir,
                'agama_karyawan' => $lama->agama_karyawan ?? 'Islam',
                'status_pernikahan' => $lama->status_pernikahan ?? 'Belum Kawin',
                'telp_karyawan' => $lama->telp_karyawan ?? '-',
                'email_karyawan' => $lama->email_karyawan ?? '-',
                'foto_karyawan' => $lama->foto_karyawan,
                'alamat_karyawan' => $lama->alamat_karyawan,
                'npwp_karyawan' => $lama->npwp_karyawan ?? null, // Ini yang Anda maksud NPWP/NPM
                'pendidikan' => $lama->pendidikan ?? null,
                'berijazah' => $lama->berijazah ?? null,
                'rekening' => $lama->rekening ?? null,
                'tanggal_masuk' => ($lama->tanggal_masuk === '0000-00-00' || empty($lama->tanggal_masuk)) ? null : $lama->tanggal_masuk,

                // PIN Mesin dari id_absensi lama
                'pin_mesin' => $lama->id_absensi,

                'is_active' => $lama->aktif,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $jumlahBaris++;
        }

        $this->info("Selesai! Berhasil memindahkan $jumlahBaris data Karyawan dan otomatis membuat tabel master Jabatan & Status.");
    }
}
