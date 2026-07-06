<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            // Polymorphic: bisa dipakai oleh Quiz, Section, dll
            $table->morphs('mediable'); // mediable_type + mediable_id
            $table->enum('type', ['image', 'video', 'audio'])->default('image');
            $table->string('disk')->default('public');
            $table->string('path');          // path relatif di storage
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable(); // bytes
            $table->unsignedInteger('order')->default(0);   // urutan tampilan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
