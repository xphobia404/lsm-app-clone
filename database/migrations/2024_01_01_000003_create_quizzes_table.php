<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('order')->default(0);
            $table->text('question');
            $table->string('option_a');
            $table->string('option_b')->nullable();
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();
            $table->enum('correct_answer', ['a', 'b', 'c', 'd']);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
