<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LearningSchemaController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\QuizController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user', 'check.active'])
    ->prefix('app')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', fn () => view('user.dashboard'))->name('dashboard');

        // Learning Schemas (read-only untuk user)
        Route::get('/schemas',                                            [LearningSchemaController::class, 'index'])->name('schemas.index');
        Route::get('/schemas/{learningSchema}',                           [LearningSchemaController::class, 'show'])->name('schemas.show');

        // Sections (read-only)
        Route::get('/schemas/{learningSchema}/sections/{section}',        [SectionController::class, 'show'])->name('sections.show');

        // Contents (read-only)
        Route::get('/sections/{section}/contents/{content}',             [ContentController::class, 'show'])->name('contents.show');

        // Quiz
        Route::get('/sections/{section}/quizzes',                        [QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/sections/{section}/quizzes/{quiz}',                 [QuizController::class, 'show'])->name('quizzes.show');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

        // ── User Management ────────────────────────────────────────────
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // ── Learning Schema Management ─────────────────────────────────
        Route::resource('learning-schemas', LearningSchemaController::class);

        // ── Section Management (nested under learning-schema) ──────────
        Route::resource('learning-schemas.sections', SectionController::class);

        // ── Content Management (nested under section) ──────────────────
        Route::resource('sections.contents', ContentController::class);

        // ── Quiz Management (nested under section) ─────────────────────
        Route::resource('sections.quizzes', QuizController::class);
    });
