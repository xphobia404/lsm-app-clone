<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_schema_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_schema_id')
                ->constrained('learning_schemas')
                ->cascadeOnDelete();
            $table->foreignId('section_id')
                ->constrained('sections')
                ->cascadeOnDelete();
            $table->unsignedInteger('section_order')->default(0);
            $table->timestamps();

            $table->unique(['learning_schema_id', 'section_id']);
            $table->index(['learning_schema_id', 'section_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_schema_section');
    }
};
