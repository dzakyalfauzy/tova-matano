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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('water_quality_map_image_url')->nullable()->after('org_chart_image_url');
            $table->string('water_quality_map_image_credit')->nullable()->after('water_quality_map_image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'water_quality_map_image_url',
                'water_quality_map_image_credit',
            ]);
        });
    }
};
