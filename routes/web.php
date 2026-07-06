<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LearningSchemaController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserProgressController;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

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

        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        // Materi
        Route::get('/schemas',                   [LearningSchemaController::class, 'userIndex'])->name('schemas.index');
        Route::get('/schemas/{learningSchema}',  [LearningSchemaController::class, 'userShow'])->name('schemas.show');

        // Section detail
        Route::get('/schemas/{learningSchema}/sections/{section}',
            [SectionController::class, 'userShow'])->name('sections.show');

        // Konten
        Route::get('/sections/{section}/contents/{content}',
            [ContentController::class, 'userShow'])->name('contents.show');

        // Quiz
        Route::get('/sections/{section}/quizzes',
            [QuizController::class, 'userIndex'])->name('quizzes.index');
        Route::get('/sections/{section}/quizzes/{quiz}',
            [QuizController::class, 'userShow'])->name('quizzes.show');
        Route::post('/sections/{section}/quizzes/submit',
            [QuizController::class, 'userSubmit'])->name('quizzes.submit');

        // Progress
        Route::post('/sections/{section}/progress',
            [UserProgressController::class, 'update'])->name('sections.progress.update');
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

        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

        // ── Users ────────────────────────────────────────────────────────
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-active',
            [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // Enrollment routes
        Route::patch('users/{user}/enrollment/status',
            [EnrollmentController::class, 'updateStatus'])->name('users.enrollment.status');
        Route::post('users/{user}/enrollment/enroll',
            [EnrollmentController::class, 'enroll'])->name('users.enrollment.enroll');
        Route::delete('users/{user}/enrollment/drop',
            [EnrollmentController::class, 'drop'])->name('users.enrollment.drop');

        // ── Learning Schemas ──────────────────────────────────────────────
        Route::resource('learning-schemas', LearningSchemaController::class);
        Route::post('learning-schemas/{learningSchema}/toggle-active',
            [LearningSchemaController::class, 'toggleActive'])->name('learning-schemas.toggle-active');
        Route::post('learning-schemas/{learningSchema}/sections/attach',
            [LearningSchemaController::class, 'attachSection'])->name('learning-schemas.sections.attach');
        Route::delete('learning-schemas/{learningSchema}/sections/{section}/detach',
            [LearningSchemaController::class, 'detachSection'])->name('learning-schemas.sections.detach');

        Route::get('learning-schemas/{learningSchema}/sections',
            [SectionController::class, 'schemaIndex'])->name('learning-schemas.sections.index');

        // ── Sections (standalone) ──────────────────────────────────────────
        Route::get('sections',                [SectionController::class, 'allIndex'])->name('sections.index');
        Route::get('sections/create',         [SectionController::class, 'create'])->name('sections.create');
        Route::post('sections',               [SectionController::class, 'store'])->name('sections.store');
        Route::get('sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit');
        Route::put('sections/{section}',      [SectionController::class, 'update'])->name('sections.update');
        Route::delete('sections/{section}',   [SectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('sections/{section}/toggle-active',
            [SectionController::class, 'toggleActive'])->name('sections.toggle-active');

        // ── Contents (nested under section) ───────────────────────────────
        Route::resource('sections.contents', ContentController::class);
        Route::post('sections/{section}/contents/{content}/toggle-active',
            [ContentController::class, 'toggleActive'])->name('sections.contents.toggle-active');

        // ── Quizzes (nested under section) ────────────────────────────────
        Route::resource('sections.quizzes', QuizController::class);
        Route::post('sections/{section}/quizzes/{quiz}/toggle-active',
            [QuizController::class, 'toggleActive'])->name('sections.quizzes.toggle-active');
    });
