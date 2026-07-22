<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absens', function (Blueprint $table) {
            $table->index('tanggal_absen');
            $table->index(['karyawan_id', 'tanggal_absen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absens', function (Blueprint $table) {
            $table->dropIndex(['tanggal_absen']);
            $table->dropIndex(['karyawan_id', 'tanggal_absen']);
        });
    }
};
