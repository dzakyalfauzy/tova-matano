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
        Schema::create('water_quality_research_references', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->unique();
            $table->string('title');
            $table->string('authors');
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->string('journal_or_institution')->nullable();
            $table->string('parameter'); // tss, ph, tds, dissolved_oxygen, temperature, clarity_cm
            $table->double('reported_minimum')->nullable();
            $table->double('reported_maximum')->nullable();
            $table->string('unit');
            $table->string('sampling_location')->nullable();
            $table->string('sampling_period')->nullable();
            $table->string('sampling_depth')->nullable();
            $table->string('measurement_method')->nullable();
            $table->string('research_type')->default('field'); // laboratory, field, remote_sensing
            $table->string('source_url_or_doi')->nullable();
            $table->string('verification_status')->default('provisional'); // verified, provisional, unverified
            $table->boolean('is_active_for_comparison')->default(true);
            $table->text('interpretation_notes')->nullable();
            $table->timestamps();

            $table->index(['parameter', 'verification_status', 'is_active_for_comparison'], 'wq_ref_param_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_quality_research_references');
    }
};
