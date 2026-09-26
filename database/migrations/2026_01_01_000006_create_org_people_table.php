<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('org_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_category_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable(); // nullable: Pelindung sering cuma nama instansi
            $table->string('role_title'); // "Gubernur ..." ATAU "Sekretaris Jenderal"
            $table->string('photo_url')->nullable();
            $table->string('divisi_name')->nullable(); // dipakai kalau kategori = Divisi, untuk grouping
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_people');
    }
};
