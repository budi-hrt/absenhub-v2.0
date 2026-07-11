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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nik')->nullable()->unique();
            $table->string('nama_karyawan');
            $table->string('jk_karyawan'); // Bisa diganti enum(['L', 'P']) jika mau lebih ketat
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->nullOnDelete();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama_karyawan');
            $table->string('status_pernikahan');
            $table->string('telp_karyawan');
            $table->string('email_karyawan');
            $table->string('foto_karyawan')->nullable();
            $table->string('alamat_karyawan')->nullable();
            $table->string('npwp_karyawan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('berijazah')->nullable();
            $table->string('rekening')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
            $table->date('tanggal_masuk')->nullable();
            $table->integer('pin_mesin')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
