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
        Schema::create('hero_slide_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hero_slide_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('cta_label')->nullable();
            
            $table->unique(['hero_slide_id', 'locale']);
        });

        // Copy existing data to default locale (id)
        $slides = DB::table('hero_slides')->get();
        foreach ($slides as $slide) {
            DB::table('hero_slide_translations')->insert([
                'hero_slide_id' => $slide->id,
                'locale' => 'id',
                'title' => $slide->title,
                'subtitle' => $slide->subtitle,
                'cta_label' => $slide->cta_label,
            ]);
        }

        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['title', 'subtitle', 'cta_label']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('cta_label')->nullable();
        });

        $translations = DB::table('hero_slide_translations')->where('locale', 'id')->get();
        foreach ($translations as $translation) {
            DB::table('hero_slides')->where('id', $translation->hero_slide_id)->update([
                'title' => $translation->title,
                'subtitle' => $translation->subtitle,
                'cta_label' => $translation->cta_label,
            ]);
        }

        Schema::dropIfExists('hero_slide_translations');
    }
};
