<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LearningSchemaController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\QuizController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Users
    Route::apiResource('users', UserController::class);

    // Learning Schemas
    Route::apiResource('learning-schemas', LearningSchemaController::class);

    // Sections (nested under learning-schema)
    Route::apiResource('learning-schemas.sections', SectionController::class);

    // Contents (nested under section)
    Route::apiResource('sections.contents', ContentController::class);

    // Quizzes (nested under section)
    Route::apiResource('sections.quizzes', QuizController::class);

});
