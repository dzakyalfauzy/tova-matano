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
            $table->string('partnership_hero_image_url')->nullable()->after('blog_hero_image_url');
            $table->string('program_hero_image_url')->nullable()->after('partnership_hero_image_url');
            $table->string('contact_hero_image_url')->nullable()->after('program_hero_image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'partnership_hero_image_url',
                'program_hero_image_url',
                'contact_hero_image_url',
            ]);
        });
    }
};
