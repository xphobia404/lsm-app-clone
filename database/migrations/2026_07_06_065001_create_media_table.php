<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable'); // mediable_type, mediable_id
            $table->enum('media_type', ['image', 'video', 'audio', 'url']);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable(); // file lokal
            $table->string('url')->nullable();       // youtube/google drive
            $table->unsignedInteger('media_order')->default(0);
            $table->timestamps();

            $table->index(['media_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
