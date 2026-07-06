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
            $table->json('answers')->nullable(); // format: [{quiz_id: 1, answer: 'a'}, ...]
            $table->unsignedInteger('score')->default(0);         // nilai mentah (jumlah benar)
            $table->unsignedSmallInteger('score_percent')->default(0); // persentase 0-100 (fix dari unsignedTinyInteger)
            $table->boolean('passed')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'section_id']);
            $table->index('section_id'); // index tambahan untuk query statistik per section
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
