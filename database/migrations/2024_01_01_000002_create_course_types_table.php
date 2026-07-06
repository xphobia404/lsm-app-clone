<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // e.g. P3K, HSE
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();        // emoji / icon class
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        // pivot: user <-> course_type (enrollment)
        Schema::create('course_type_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'course_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_type_user');
        Schema::dropIfExists('course_types');
    }
};
