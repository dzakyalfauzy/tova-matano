<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Setiap accordion item di halaman Struktur Organisasi = 1 kategori.
// display_type menentukan cara render: ROLE_LIST (Pelindung/Penasehat)
// atau PERSON_GRID (Dewan Pakar/Badan Eksekutif/Pengurus/Divisi).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('org_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Pelindung", "Penasehat", dst
            $table->string('description')->nullable();
            $table->enum('display_type', ['ROLE_LIST', 'PERSON_GRID']);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_categories');
    }
};
