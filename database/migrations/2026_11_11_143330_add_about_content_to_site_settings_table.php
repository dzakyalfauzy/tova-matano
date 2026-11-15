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
            $table->json('about_content')->nullable()->after('fixed_text');
            $table->string('about_lake_image_credit')->nullable()->after('about_lake_image_url');
            $table->boolean('about_legal_verified')->default(false)->after('about_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_content',
                'about_lake_image_credit',
                'about_legal_verified',
            ]);
        });
    }
};
