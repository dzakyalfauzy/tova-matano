<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan parameter pemantauan baru tanpa mengubah data historis.
     * Semua kolom nullable agar catatan lama (pH & kejernihan saja) tetap valid.
     */
    public function up(): void
    {
        Schema::table('water_quality_records', function (Blueprint $table) {
            $table->double('tss')->nullable()->after('clarity_cm');
            $table->double('tds')->nullable()->after('tss');
            $table->double('dissolved_oxygen')->nullable()->after('tds');
            $table->double('temperature')->nullable()->after('dissolved_oxygen');
        });
    }

    public function down(): void
    {
        Schema::table('water_quality_records', function (Blueprint $table) {
            $table->dropColumn(['tss', 'tds', 'dissolved_oxygen', 'temperature']);
        });
    }
};
