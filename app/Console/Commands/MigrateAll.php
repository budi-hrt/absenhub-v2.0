<?php

namespace App\Console\Commands;

use App\Models\Karyawan;
use App\Models\User;
use Database\Seeders\FeatureFlagSeeder;
use Database\Seeders\LokasiAbsenSeeder;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MigrateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-all
                            {--fresh : Reset ulang seluruh tabel dari awal (migrate:fresh)}
                            {--skip-legacy : Lewati proses penarikan data lama dari database CI3}
                            {--truncate-absen : Bersihkan tabel absensi sebelum menarik data lama dari CI3}
                            {--create-users : Buatkan akun login otomatis untuk karyawan yang belum memiliki akun}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup all-in-one AbsenHub v2: update struktur database, seeders, dan migrasi data lama CI3';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->newLine();
        $this->info('===========================================================');
        $this->info('         ABSENHUB v2.0 - MASTER SETUP & MIGRATION          ');
        $this->info('===========================================================');
        $this->newLine();

        // ---------------------------------------------------------------------
        // LANGKAH 1: UPDATE STRUKTUR TABEL (MIGRATIONS)
        // ---------------------------------------------------------------------
        $this->info('▶ [1/5] Memeriksa & Memperbarui Struktur Tabel (Migrations)...');

        if ($this->option('fresh')) {
            $this->warn('  Menjalankan migrate:fresh (Menghapus seluruh tabel dan membuat ulang)...');
            $this->call('migrate:fresh', ['--force' => true]);
        } else {
            $this->info('  Menjalankan migrate --force (Menambahkan tabel/kolom baru tanpa merusak data lama)...');
            $this->call('migrate', ['--force' => true]);
        }
        $this->info('  ✓ Struktur tabel database terbaru berhasil diaplikasikan.');
        $this->newLine();

        // ---------------------------------------------------------------------
        // LANGKAH 2: SEEDERS DASAR (Role, Permission, Akun Admin, Master Fitur)
        // ---------------------------------------------------------------------
        $this->info('▶ [2/5] Menjalankan Seeder Data Awal...');

        $this->info('  - Sinkronisasi Role, Permission & Akun Admin default...');
        $this->call('db:seed', ['--class' => RoleAndUserSeeder::class, '--force' => true]);

        $this->info('  - Setup Master Lokasi Absensi...');
        $this->call('db:seed', ['--class' => LokasiAbsenSeeder::class, '--force' => true]);

        $this->info('  - Setup Pengaturan Feature Flags...');
        $this->call('db:seed', ['--class' => FeatureFlagSeeder::class, '--force' => true]);

        $this->info('  ✓ Master data awal berhasil disiapkan.');
        $this->newLine();

        // ---------------------------------------------------------------------
        // LANGKAH 3: CEK & MIGRASI DATA LAMA (CI3)
        // ---------------------------------------------------------------------
        $this->info('▶ [3/5] Memeriksa Database CI3 Lama (mysql_lama)...');

        if ($this->option('skip-legacy')) {
            $this->warn('  Opsi --skip-legacy aktif. Melewati penarikan data dari database lama.');
        } else {
            $legacyConnected = false;
            try {
                DB::connection('mysql_lama')->getPdo();
                $legacyConnected = true;
            } catch (\Throwable $e) {
                $this->warn('  [INFO] Database lama (mysql_lama) tidak terhubung atau belum disiapkan.');
                $this->line('  Pesan: ' . $e->getMessage());
                $this->warn('  Melanjutkan instalasi tanpa penarikan data lama (Mode Clean Install).');
            }

            if ($legacyConnected) {
                $this->info('  ✓ Terhubung ke database CI3 lama. Memulai migrasi data...');

                // 3a. Karyawan & Master Jabatan/Status
                $this->info('  - Migrasi Karyawan, Jabatan, dan Status...');
                $this->call('app:migrate-data');

                // 3b. Master Masa Kontrak, Penandatangan, dan Data Kontrak
                $this->info('  - Migrasi Kontrak & Penandatangan...');
                $this->call('app:migrate-kontrak');

                // 3c. Riwayat Karyawan Nonaktif
                $this->info('  - Migrasi Riwayat Nonaktif...');
                $this->call('app:migrate-nonaktif');

                // 3d. Data Log Absensi
                $this->info('  - Migrasi Log Absensi...');
                $absenArgs = [];
                if ($this->option('truncate-absen') || $this->option('fresh')) {
                    $absenArgs['--truncate'] = true;
                }
                $this->call('app:migrate-absen', $absenArgs);

                $this->info('  ✓ Seluruh data lama berhasil dipindahkan ke AbsenHub v2.');
            }
        }
        $this->newLine();

        // ---------------------------------------------------------------------
        // LANGKAH 4: GENERATE AKUN USER KARYAWAN (OPSIONAL)
        // ---------------------------------------------------------------------
        if ($this->option('create-users')) {
            $this->info('▶ [4/5] Membuat Akun Login untuk Karyawan yang Belum Memiliki Akun...');
            $this->createUsersForKaryawan();
            $this->newLine();
        } else {
            $this->info('▶ [4/5] Lewati pembuatan akun user karyawan (Gunakan --create-users jika ingin buat otomatis).');
            $this->newLine();
        }

        // ---------------------------------------------------------------------
        // LANGKAH 5: OPTIMASI & BERSIHKAN CACHE
        // ---------------------------------------------------------------------
        $this->info('▶ [5/5] Membersihkan Cache Aplikasi...');
        $this->call('optimize:clear');
        $this->info('  ✓ Cache dibersihkan.');
        $this->newLine();

        // ---------------------------------------------------------------------
        // SELESAI
        // ---------------------------------------------------------------------
        $this->info('===========================================================');
        $this->info('  🎉 SETUP SELESAI! ABSENHUB v2.0 SIAP DIGUNAKAN           ');
        $this->info('===========================================================');
        $this->line('Akun Admin Default:');
        $this->line('  - Email   : admin@mail.com');
        $this->line('  - Password: password');
        $this->line('Akun Super Admin:');
        $this->line('  - Email   : budi.hrt@outlook.co.id');
        $this->line('  - Password: titikKoma');
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Buatkan akun user untuk karyawan yang belum memiliki user_id
     */
    protected function createUsersForKaryawan(): void
    {
        $karyawans = Karyawan::whereNull('user_id')->where('is_active', true)->get();
        $totalCreated = 0;
        $roleKaryawan = Role::firstOrCreate(['name' => 'karyawan']);

        foreach ($karyawans as $karyawan) {
            $email = $karyawan->email_karyawan;

            // Jika email tidak valid atau strip, gunakan NIK sebagai format email
            if (empty($email) || $email === '-' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = ($karyawan->nik ?? 'karyawan_' . $karyawan->id) . '@absenhub.local';
            }

            // Pastikan email unik di tabel users
            if (User::where('email', $email)->exists()) {
                $email = ($karyawan->nik ?? $karyawan->id) . '_' . $karyawan->id . '@absenhub.local';
            }

            // Password default menggunakan NIK atau fallback 'password'
            $defaultPassword = ! empty($karyawan->nik) ? $karyawan->nik : 'password';

            $user = User::create([
                'name' => $karyawan->nama_karyawan,
                'email' => $email,
                'password' => Hash::make($defaultPassword),
                'is_active' => true,
            ]);

            $user->assignRole($roleKaryawan);

            // Hubungkan karyawan ke user baru
            $karyawan->update(['user_id' => $user->id]);
            $totalCreated++;
        }

        $this->info("  ✓ Berhasil membuat $totalCreated akun login baru untuk karyawan aktif.");
    }
}
