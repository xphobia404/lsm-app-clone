<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah course_type_id ke sections
        Schema::table('sections', function (Blueprint $table) {
            $table->foreignId('course_type_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('course_types')
                  ->nullOnDelete();
        });

        // Tambah course_type_id ke users (spesialisasi pilihan user)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('course_type_id')
                  ->nullable()
                  ->after('is_active')
                  ->constrained('course_types')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['course_type_id']);
            $table->dropColumn('course_type_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['course_type_id']);
            $table->dropColumn('course_type_id');
        });
    }
};
