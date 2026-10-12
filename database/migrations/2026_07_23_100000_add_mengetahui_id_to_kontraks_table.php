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
        Schema::table('kontraks', function (Blueprint $table) {
            $table->foreignId('mengetahui_id')
                ->nullable()
                ->after('penandatangan_id')
                ->constrained('penandatangans')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontraks', function (Blueprint $table) {
            $table->dropForeign(['mengetahui_id']);
            $table->dropColumn('mengetahui_id');
        });
    }
};
