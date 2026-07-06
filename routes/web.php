<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\SectionController as UserSection;
use App\Http\Controllers\User\QuizController as UserQuiz;
use App\Http\Controllers\User\CourseTypeController as UserCourseType;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\SectionController as AdminSection;
use App\Http\Controllers\Admin\QuizController as AdminQuiz;
use App\Http\Controllers\Admin\CourseTypeController as AdminCourseType;
use App\Http\Controllers\Admin\MediaController as AdminMedia;
// New CRUD Controllers
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
        Route::get('/pilih-spesialisasi', [UserCourseType::class, 'select'])->name('course-type.select');
        Route::post('/pilih-spesialisasi', [UserCourseType::class, 'store'])->name('course-type.store');

        Route::get('/dashboard',           [UserDashboard::class, 'index'])->name('dashboard');
        Route::get('/courses',             [UserDashboard::class, 'courses'])->name('courses');
        Route::get('/history',             [UserDashboard::class, 'history'])->name('history');

        Route::get('/section/{section}',             [UserSection::class, 'show'])->name('section.show');
        Route::get('/section/{section}/quiz',        [UserQuiz::class, 'show'])->name('quiz.show');
        Route::post('/section/{section}/quiz',       [UserQuiz::class, 'submit'])->name('quiz.submit');
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
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // ── Course Type Management (lama) ───────────────────────────
        Route::resource('course-types', AdminCourseType::class)->except(['show']);
        Route::post('/course-types/{courseType}/toggle-active', [AdminCourseType::class, 'toggleActive'])->name('course-types.toggle-active');

        // ── User Management ────────────────────────────────────────
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/reset-password', [AdminUser::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/toggle-active',  [AdminUser::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/reset-progress', [AdminUser::class, 'resetProgress'])->name('users.reset-progress');

        // ── Learning Schema Management ──────────────────────────────
        Route::resource('learning-schemas', LearningSchemaController::class);

        // ── Section Management (nested under learning-schema) ───────
        Route::resource('learning-schemas.sections', SectionController::class);
        Route::post(
            '/learning-schemas/{learningSchema}/sections/{section}/toggle-publish',
            [AdminSection::class, 'togglePublish']
        )->name('learning-schemas.sections.toggle-publish');

        // ── Content Management (nested under section) ───────────────
        Route::resource('sections.contents', ContentController::class);

        // ── Quiz Management (nested under section) ──────────────────
        Route::resource('sections.quizzes', QuizController::class);

        // ── Media Management ────────────────────────────────────────
        Route::prefix('sections/{section}')
            ->name('sections.')
            ->group(function () {
                Route::post('/media',               [AdminMedia::class, 'storeForSection'])->name('media.store-section');
                Route::post('/quizzes/{quiz}/media',[AdminMedia::class, 'storeForQuiz'])->name('quizzes.media.store');
            });

        Route::delete('/media/{media}', [AdminMedia::class, 'destroy'])->name('media.destroy');
        Route::put('/media/reorder',    [AdminMedia::class, 'reorder'])->name('media.reorder');
    });
