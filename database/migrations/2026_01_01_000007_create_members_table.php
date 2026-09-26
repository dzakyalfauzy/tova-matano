<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo_url')->nullable();
            $table->string('divisi_name')->nullable();
            $table->integer('points')->default(0); // admin input/edit manual
            $table->text('message')->nullable(); // pesan lingkungan, tampil di Portal Keanggotaan
            $table->timestamp('joined_at')->useCurrent();
            $table->boolean('is_featured')->default(true); // exclude dari portal tanpa hapus data
            $table->timestamps();

            $table->index('points'); // buat query ORDER BY points DESC efisien
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
