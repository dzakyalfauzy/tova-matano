<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('water_quality_records', function (Blueprint $table) {
            $table->foreignId('water_sampling_point_id')->nullable()->constrained('water_sampling_points')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('water_quality_records', function (Blueprint $table) {
            $table->dropForeign(['water_sampling_point_id']);
            $table->dropColumn('water_sampling_point_id');
        });
    }
};
