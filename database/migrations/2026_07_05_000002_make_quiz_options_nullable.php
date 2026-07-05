<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('option_b')->nullable()->change();
            $table->string('option_c')->nullable()->change();
            $table->string('option_d')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Isi dulu dengan nilai default sebelum set NOT NULL kembali
            \DB::statement("UPDATE quizzes SET option_b = '-' WHERE option_b IS NULL");
            \DB::statement("UPDATE quizzes SET option_c = '-' WHERE option_c IS NULL");
            \DB::statement("UPDATE quizzes SET option_d = '-' WHERE option_d IS NULL");

            $table->string('option_b')->nullable(false)->change();
            $table->string('option_c')->nullable(false)->change();
            $table->string('option_d')->nullable(false)->change();
        });
    }
};
