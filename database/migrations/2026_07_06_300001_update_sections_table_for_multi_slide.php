<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Update sections table:
 * - Hapus kolom single-media lama (media_type, media_file, media_url, content)
 *   yang sudah digantikan oleh sistem multi-slide berbasis JSON (pages).
 * - Pastikan kolom content_mode & pages ada.
 * - Pastikan kolom slug ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {

            // ── Tambah kolom baru jika belum ada ──────────────────────────────

            if (!Schema::hasColumn('sections', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('title');
            }

            if (!Schema::hasColumn('sections', 'content_mode')) {
                $table->string('content_mode')->default('multi')->after('description');
            }

            if (!Schema::hasColumn('sections', 'pages')) {
                // JSON array of slides. Each slide:
                // {
                //   title, content,
                //   image_url, image_path,
                //   video_url, video_path,
                //   audio_url, audio_path,
                //   youtube_url, drive_url
                // }
                $table->json('pages')->nullable()->after('content_mode');
            }

            // ── Hapus kolom single-media lama jika masih ada ──────────────────

            $dropCols = [];

            foreach (['media_type', 'media_file', 'media_url'] as $col) {
                if (Schema::hasColumn('sections', $col)) {
                    $dropCols[] = $col;
                }
            }

            // Kolom 'content' (single-page rich text) sudah tidak dipakai.
            // Data lama dipindahkan ke pages[0].content di bawah.
            // JANGAN drop jika masih dipakai oleh query/model lain —
            // comment baris ini jika belum siap.
            // if (Schema::hasColumn('sections', 'content')) {
            //     $dropCols[] = 'content';
            // }

            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });

        // ── Migrasi data lama: pindahkan content lama ke pages[0] ─────────────
        // Jika ada section lama yang punya content tapi belum ada pages,
        // bungkus jadi slide pertama otomatis.
        \DB::table('sections')
            ->whereNull('pages')
            ->orWhere('pages', '[]')
            ->orWhere('pages', '')
            ->whereNotNull('content')
            ->where('content', '!=', '')
            ->each(function ($section) {
                \DB::table('sections')
                    ->where('id', $section->id)
                    ->update([
                        'pages' => json_encode([
                            [
                                'title'       => 'Halaman 1',
                                'content'     => $section->content,
                                'image_url'   => null,
                                'image_path'  => null,
                                'video_url'   => null,
                                'video_path'  => null,
                                'audio_url'   => null,
                                'audio_path'  => null,
                                'youtube_url' => null,
                                'drive_url'   => null,
                            ]
                        ]),
                        'content_mode' => 'multi',
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Kembalikan kolom single-media jika rollback
            if (!Schema::hasColumn('sections', 'media_type')) {
                $table->string('media_type')->default('video_upload');
            }
            if (!Schema::hasColumn('sections', 'media_file')) {
                $table->string('media_file')->nullable();
            }
            if (!Schema::hasColumn('sections', 'media_url')) {
                $table->string('media_url')->nullable();
            }
        });
    }
};
