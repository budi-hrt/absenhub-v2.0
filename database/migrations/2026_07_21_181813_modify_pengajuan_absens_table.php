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
        Schema::table('pengajuan_absens', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
            $table->json('tanggal')->after('jenis')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_absens', function (Blueprint $table) {
            $table->dropColumn('tanggal');
            $table->date('tanggal_mulai')->after('jenis')->nullable();
            $table->date('tanggal_selesai')->after('tanggal_mulai')->nullable();
        });
    }
};
