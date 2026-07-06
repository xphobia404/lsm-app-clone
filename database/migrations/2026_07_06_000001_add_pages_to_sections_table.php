<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // 'single' = 1 konten seperti sekarang
            // 'multi'  = konten berupa array of pages (JSON)
            $table->string('content_mode')->default('single')->after('content');

            // Menyimpan array pages: [{title, content, image_url}, ...]
            $table->json('pages')->nullable()->after('content_mode');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['content_mode', 'pages']);
        });
    }
};
