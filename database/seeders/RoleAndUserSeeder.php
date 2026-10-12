<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan Cache Spatie agar tidak tersangkut di memory
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Daftar Permission Dasar dan Fitur
        $permissions = [
            ['name' => 'lihat-admin', 'group' => 'Manajemen Admin'],
            ['name' => 'tambah-admin', 'group' => 'Manajemen Admin'],
            ['name' => 'ubah-admin', 'group' => 'Manajemen Admin'],
            ['name' => 'hapus-admin', 'group' => 'Manajemen Admin'],

            ['name' => 'lihat-user-karyawan', 'group' => 'Manajemen User'],
            ['name' => 'tambah-user-karyawan', 'group' => 'Manajemen User'],
            ['name' => 'ubah-user-karyawan', 'group' => 'Manajemen User'],
            ['name' => 'hapus-user-karyawan', 'group' => 'Manajemen User'],

            ['name' => 'lihat-karyawan', 'group' => 'Manajemen Karyawan'],
            ['name' => 'tambah-karyawan', 'group' => 'Manajemen Karyawan'],
            ['name' => 'ubah-karyawan', 'group' => 'Manajemen Karyawan'],
            ['name' => 'hapus-karyawan', 'group' => 'Manajemen Karyawan'],
            ['name' => 'karyawan-list', 'group' => 'Manajemen Karyawan'],
            ['name' => 'karyawan-create', 'group' => 'Manajemen Karyawan'],
            ['name' => 'karyawan-edit', 'group' => 'Manajemen Karyawan'],
            ['name' => 'karyawan-delete', 'group' => 'Manajemen Karyawan'],

            ['name' => 'absen-list', 'group' => 'Manajemen Absensi'],
            ['name' => 'absen-update', 'group' => 'Manajemen Absensi'],

            ['name' => 'pengajuan-list', 'group' => 'Manajemen Pengajuan'],
            ['name' => 'pengajuan-proses', 'group' => 'Manajemen Pengajuan'],
            ['name' => 'pengajuan-setuju', 'group' => 'Manajemen Pengajuan'],
            ['name' => 'pengajuan-tolak', 'group' => 'Manajemen Pengajuan'],

            ['name' => 'lihat-jabatan', 'group' => 'Master Data'],
            ['name' => 'lihat-status-kerja', 'group' => 'Master Data'],
            ['name' => 'lihat-masa-kontrak', 'group' => 'Master Data'],
            ['name' => 'lihat-penandatangan', 'group' => 'Master Data'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], ['group' => $p['group']]);
        }

        // 3. Daftar Role
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleManager = Role::firstOrCreate(['name' => 'manager']);
        $roleOperator = Role::firstOrCreate(['name' => 'operator']);
        $roleKaryawan = Role::firstOrCreate(['name' => 'karyawan']);

        // 4. Hubungkan Permission ke Role
        $roleSuperAdmin->syncPermissions(Permission::all());

        $roleAdmin->syncPermissions([
            'lihat-user-karyawan',
            'tambah-user-karyawan',
            'ubah-user-karyawan',
            'lihat-admin',
            'lihat-karyawan',
            'tambah-karyawan',
            'ubah-karyawan',
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
            'lihat-jabatan',
            'lihat-status-kerja',
            'lihat-masa-kontrak',
            'lihat-penandatangan',
        ]);

        // 5. Buat Akun Admin Default (Idempotent: Tidak merusak jika sudah ada)
        $superAdmin = User::firstOrCreate(
            ['email' => 'budi.hrt@outlook.co.id'],
            [
                'name' => 'Super Admin Utama',
                'password' => Hash::make('titikKoma'),
                'is_active' => true,
            ]
        );
        if (! $superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole($roleSuperAdmin);
        }

        $adminWeb = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin AbsenHub',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        if (! $adminWeb->hasRole('admin')) {
            $adminWeb->assignRole($roleAdmin);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
