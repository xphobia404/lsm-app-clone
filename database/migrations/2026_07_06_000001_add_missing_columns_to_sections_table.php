<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Slug untuk URL-friendly identifier
            $table->string('slug')->nullable()->unique()->after('title');

            // Pages: array of slides [{title, content, image_url, video_url, ...}]
            $table->json('pages')->nullable()->after('content');

            // Thumbnail cover section
            $table->string('thumbnail')->nullable()->after('pages');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['slug', 'pages', 'thumbnail']);
        });
    }
};
