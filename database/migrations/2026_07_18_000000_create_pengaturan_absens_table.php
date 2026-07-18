<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_absens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengaturan');
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->integer('toleransi_menit')->default(10);
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_absens');
    }
};
