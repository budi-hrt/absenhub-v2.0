<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixKaryawanActiveStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $affected = DB::table('karyawans')
            ->where('is_active', 2)
            ->update(['is_active' => 0]);

        $this->command->info("Berhasil memperbarui {$affected} data karyawan dengan is_active = 2 menjadi 0.");
    }
}
