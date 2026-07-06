<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Quiz sudah mendukung media via polymorphic (mediable_type = App\Models\Quiz).
 * Tabel media sudah ada kolom morphs('mediable').
 * Migration ini tidak perlu alter tabel — hanya dokumentasi bahwa
 * Quiz sekarang terdaftar sebagai mediable morph map.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Tidak ada perubahan skema — Quiz menggunakan tabel media yang sudah ada
        // via polymorphic: mediable_type = 'App\\Models\\Quiz', mediable_id = quiz.id
    }

    public function down(): void
    {
        // Hapus semua media milik Quiz jika di-rollback
        \DB::table('media')->where('mediable_type', 'App\\Models\\Quiz')->delete();
    }
};
