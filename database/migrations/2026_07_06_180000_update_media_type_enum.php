<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah enum langsung via raw SQL
        DB::statement("
            ALTER TABLE media
            MODIFY COLUMN media_type
            ENUM('image','video','audio','url','youtube','google_drive')
            NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE media
            MODIFY COLUMN media_type
            ENUM('image','video','audio','url')
            NOT NULL
        ");
    }
};
