<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('course_type_id')
                ->nullable()
                ->constrained('course_types')
                ->nullOnDelete();

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->unsignedTinyInteger('passing_score')->default(70); // passing grade per section (0-100)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
