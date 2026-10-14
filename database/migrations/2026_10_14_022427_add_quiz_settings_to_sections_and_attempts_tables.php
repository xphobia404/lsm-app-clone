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
        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedTinyInteger('passing_score')->default(70)->after('description');
            $table->boolean('randomize_questions')->default(false)->after('passing_score');
            $table->boolean('randomize_options')->default(false)->after('randomize_questions');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->unsignedTinyInteger('passing_score')->default(70)->after('correct_answers');
            $table->boolean('is_passed')->default(false)->after('passing_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['passing_score', 'is_passed']);
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['passing_score', 'randomize_questions', 'randomize_options']);
        });
    }
};
