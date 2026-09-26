<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_quality_records', function (Blueprint $table) {
            $table->id();
            $table->timestamp('recorded_at');
            $table->float('ph');
            $table->float('clarity_cm')->nullable(); // kejernihan (secchi disk), opsional
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_quality_records');
    }
};
