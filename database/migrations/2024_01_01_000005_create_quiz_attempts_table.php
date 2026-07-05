<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('attempt_number');
            // answers: { "quiz_id": "a", "quiz_id": "c", ... }
            $table->json('answers')->nullable();
            // score = jumlah soal benar (raw)
            $table->unsignedInteger('score')->default(0);
            // score_percent = persentase benar (0-100)
            $table->unsignedTinyInteger('score_percent')->default(0);
            $table->boolean('passed')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
