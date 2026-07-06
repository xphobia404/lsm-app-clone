<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->nullable()->unique();
            $table->string('title');
            $table->text('description')->nullable();

            // Multi-slide content mode only.
            // Each slide can contain: title, content (rich text),
            // image, video, audio, youtube_url, drive_url.
            // Schema per slide (stored as JSON array):
            // {
            //   "title":       string|null,
            //   "content":     string|null,   -- Quill HTML
            //   "image_url":   string|null,
            //   "image_path":  string|null,   -- storage path
            //   "video_url":   string|null,
            //   "video_path":  string|null,
            //   "audio_url":   string|null,
            //   "audio_path":  string|null,
            //   "youtube_url": string|null,
            //   "drive_url":   string|null
            // }
            $table->json('pages')->nullable();

            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
