<?php
// FILE INI SUDAH DIKONSOLIDASIKAN KE: 2024_01_01_000003_create_sections_table.php
// Tetap ada di sini agar 'migrations' table di DB tidak error saat fresh.
// Jika fresh install (migrate:fresh), file ini aman dijalankan (no-op).

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void   { /* no-op: already in create_sections_table */ }
    public function down(): void { /* no-op */ }
};
