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
        Schema::create('kontraks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->date('tanggal_surat');
            $table->foreignId('penandatangan_id')->nullable()->constrained('penandatangans')->nullOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->foreignId('masa_kontrak_id')->nullable()->constrained('masa_kontraks')->nullOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->integer('gaji');
            $table->integer('tunjangan');
            $table->integer('um_dalamkota');
            $table->integer('um_luarkota');
            $table->string('doc_ttd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontraks');
    }
};
