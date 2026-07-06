<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_contents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_id')
                ->constrained('sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->text('description')->nullable();

            $table->foreignId('media_id')
                ->nullable()
                ->constrained('media')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
