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
        Schema::create('pengajuan_absens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();

            // Jenis: Cuti, Izin, Sakit
            $table->string('jenis');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->text('keterangan')->nullable();

            // Foto bukti surat dokter / dll
            $table->string('lampiran')->nullable();

            // Status: Menunggu, Disetujui, Ditolak
            $table->string('status')->default('Menunggu');

            // Siapa admin yang menyetujui/menolak (optional)
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('alasan_tolak')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_absens');
    }
};
