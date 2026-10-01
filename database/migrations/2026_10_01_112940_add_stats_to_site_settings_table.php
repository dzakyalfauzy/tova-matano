<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('stat_sampah_terangkut')->nullable()->default('2,4T');
            $table->string('stat_sekolah_teredukasi')->nullable()->default('8');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['stat_sampah_terangkut', 'stat_sekolah_teredukasi']);
        });
    }
};
