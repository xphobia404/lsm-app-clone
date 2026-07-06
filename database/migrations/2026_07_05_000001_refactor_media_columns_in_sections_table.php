<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // 1. Tambah kolom baru
            $table->string('media_type')->default('video_upload')->after('content');
            $table->string('media_file')->nullable()->after('media_type');
            $table->string('media_url')->nullable()->after('media_file');
        });

        // 2. Migrasi data lama ke kolom baru
        //    video_type: 'upload' → 'video_upload' | 'youtube' → 'youtube'
        //    video: jika upload → media_file, jika youtube → media_url
        DB::statement("
            UPDATE sections
            SET
                media_type = CASE
                    WHEN video_type = 'youtube' THEN 'youtube'
                    ELSE 'video_upload'
                END,
                media_file = CASE
                    WHEN video_type != 'youtube' THEN video
                    ELSE NULL
                END,
                media_url = CASE
                    WHEN video_type = 'youtube' THEN video
                    ELSE NULL
                END
        ");

        Schema::table('sections', function (Blueprint $table) {
            // 3. Hapus kolom lama setelah data dipindahkan
            $table->dropColumn(['video', 'video_type']);
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Kembalikan kolom lama
            $table->string('video')->nullable()->after('content');
            $table->string('video_type')->default('upload')->after('video');
        });

        // Kembalikan data
        DB::statement("
            UPDATE sections
            SET
                video = COALESCE(media_file, media_url),
                video_type = CASE
                    WHEN media_type = 'youtube' THEN 'youtube'
                    ELSE 'upload'
                END
        ");

        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'media_file', 'media_url']);
        });
    }
};
