<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission; // IMPORT INI
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// IMPORT INI

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan Cache Spatie agar tidak tersangkut di memory
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. MATIKAN CEK FOREIGN KEY agar bisa menghapus data relasi dengan aman
        Schema::disableForeignKeyConstraints();

        // 3. PROSES PENGOSONGAN DATA LAMA (Proses Timpa)
        // Kosongkan tabel pivot/penghubung terlebih dahulu
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();

        // Kosongkan tabel utama Role dan Permission
        Role::truncate();
        Permission::truncate();

        // Hapus user admin lama jika emailnya sama agar tidak bentrok saat dibuat ulang
        User::where('email', 'admin@mail.com')->delete();
        User::where('email', 'budi.hrt@outlook.co.id')->delete();

        // HIDUPKAN KEMBALI CEK FOREIGN KEY
        Schema::enableForeignKeyConstraints();

        // ========================================================
        // 4. MULAI ISI DATA BARU SPERTI BIASA
        // ========================================================

        // Buat List Permission
        Permission::create(['name' => 'lihat-admin', 'group' => 'Manajemen Admin']);
        Permission::create(['name' => 'tambah-admin', 'group' => 'Manajemen Admin']);
        Permission::create(['name' => 'ubah-admin', 'group' => 'Manajemen Admin']);
        Permission::create(['name' => 'hapus-admin', 'group' => 'Manajemen Admin']);

        Permission::create(['name' => 'lihat-user-karyawan', 'group' => 'Manajemen User']);
        Permission::create(['name' => 'tambah-user-karyawan', 'group' => 'Manajemen User']);
        Permission::create(['name' => 'ubah-user-karyawan', 'group' => 'Manajemen User']);
        Permission::create(['name' => 'hapus-user-karyawan', 'group' => 'Manajemen User']);

        Permission::create(['name' => 'lihat-karyawan', 'group' => 'Manajemen Karyawan']);
        Permission::create(['name' => 'tambah-karyawan', 'group' => 'Manajemen Karyawan']);
        Permission::create(['name' => 'ubah-karyawan', 'group' => 'Manajemen Karyawan']);
        Permission::create(['name' => 'hapus-karyawan', 'group' => 'Manajemen Karyawan']);

        Permission::create(['name' => 'absen-list', 'group' => 'Manajemen Absensi']);
        Permission::create(['name' => 'absen-update', 'group' => 'Manajemen Absensi']);

        Permission::create(['name' => 'pengajuan-list', 'group' => 'Manajemen Pengajuan']);
        Permission::create(['name' => 'pengajuan-proses', 'group' => 'Manajemen Pengajuan']);
        Permission::create(['name' => 'pengajuan-setuju', 'group' => 'Manajemen Pengajuan']);
        Permission::create(['name' => 'pengajuan-tolak', 'group' => 'Manajemen Pengajuan']);

        // Buat List Role
        $roleSuperAdmin = Role::create(['name' => 'super-admin']);
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleManager = Role::create(['name' => 'manager']);
        $roleOperator = Role::create(['name' => 'operator']);
        $roleKaryawan = Role::create(['name' => 'karyawan']);

        // Hubungkan Permission ke Role
        $roleSuperAdmin->givePermissionTo(Permission::all());
        $roleAdmin->givePermissionTo([
            'lihat-user-karyawan',
            'tambah-user-karyawan',
            'ubah-user-karyawan',
            'lihat-admin',
            'karyawan-list',
            'karyawan-create',
            'karyawan-edit',
            'karyawan-delete',
            'absen-list',
            'absen-update',
            'pengajuan-list',
            'pengajuan-proses',
            'pengajuan-setuju',
            'pengajuan-tolak',
        ]);

        // Buat User Contoh
        $superAdmin = User::create([
            'name' => 'Super Admin Utama',
            'email' => 'budi.hrt@outlook.co.id',
            'password' => Hash::make('titikKoma'),
            'is_active' => true,
        ]);
        $superAdmin->assignRole($roleSuperAdmin);

        $adminWeb = User::create([
            'name' => 'Admin AbsenHub',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $adminWeb->assignRole($roleAdmin);
    }
}
