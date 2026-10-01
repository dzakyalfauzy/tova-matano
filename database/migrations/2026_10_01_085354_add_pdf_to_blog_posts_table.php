<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('pdf_url')->nullable()->after('content');
            // Allow longText to be nullable since we might only have a PDF
            $table->longText('content')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('pdf_url');
            $table->longText('content')->nullable(false)->change();
        });
    }
};
