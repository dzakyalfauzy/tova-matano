<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL/MariaDB we can change enums or just use string
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('category')->change();
        });
    }

    public function down(): void
    {
        // Revert not strictly needed unless strictly rolling back
    }
};
