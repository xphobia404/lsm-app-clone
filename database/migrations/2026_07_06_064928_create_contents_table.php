<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('sections')
                ->cascadeOnDelete();

            $table->string('title');
            $table->enum('content_type', ['text', 'video', 'file', 'url'])->default('text');
            $table->longText('body')->nullable();
            $table->string('url', 2000)->nullable();
            $table->unsignedInteger('content_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section_id', 'content_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
