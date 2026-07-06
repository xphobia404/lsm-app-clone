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
        // Pilih spesialisasi (boleh akses sebelum pilih)
        Route::get('/pilih-spesialisasi', [UserCourseType::class, 'select'])->name('course-type.select');
        Route::post('/pilih-spesialisasi', [UserCourseType::class, 'store'])->name('course-type.store');

        Route::get('/dashboard',           [UserDashboard::class, 'index'])->name('dashboard');
        Route::get('/courses',             [UserDashboard::class, 'courses'])->name('courses');

        // [FITUR BARU] Riwayat quiz attempts
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

        // Course Type Management
        Route::resource('course-types', AdminCourseType::class)->except(['show']);
        Route::post('/course-types/{courseType}/toggle-active', [AdminCourseType::class, 'toggleActive'])->name('course-types.toggle-active');

        // User Management
        Route::resource('users', AdminUser::class)->except(['show']);
        Route::get('/users/{user}/detail',          [AdminUser::class, 'show'])->name('users.show');
        Route::post('/users/{user}/reset-password', [AdminUser::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/toggle-active',  [AdminUser::class, 'toggleActive'])->name('users.toggle-active');
        // [FITUR BARU] Reset progress user per spesialisasi
        Route::post('/users/{user}/reset-progress', [AdminUser::class, 'resetProgress'])->name('users.reset-progress');

        // Section Management
        Route::resource('sections', AdminSection::class);
        Route::post('/sections/{section}/toggle-publish', [AdminSection::class, 'togglePublish'])->name('sections.toggle-publish');

        // Quiz Management (nested under sections)
        Route::prefix('sections/{section}')
            ->name('sections.')
            ->group(function () {
                Route::get('/quizzes',              [AdminQuiz::class, 'index'])->name('quizzes.index');
                Route::get('/quizzes/create',       [AdminQuiz::class, 'create'])->name('quizzes.create');
                Route::post('/quizzes',             [AdminQuiz::class, 'store'])->name('quizzes.store');
                Route::get('/quizzes/{quiz}/edit',  [AdminQuiz::class, 'edit'])->name('quizzes.edit');
                Route::put('/quizzes/{quiz}',       [AdminQuiz::class, 'update'])->name('quizzes.update');
                Route::delete('/quizzes/{quiz}',    [AdminQuiz::class, 'destroy'])->name('quizzes.destroy');

                // Media Management
                Route::post('/media',               [AdminMedia::class, 'storeForSection'])->name('media.store-section');
                Route::post('/quizzes/{quiz}/media',[AdminMedia::class, 'storeForQuiz'])->name('quizzes.media.store');
            });

        // Media Management (standalone)
        Route::delete('/media/{media}',             [AdminMedia::class, 'destroy'])->name('media.destroy');
        Route::put('/media/reorder',                [AdminMedia::class, 'reorder'])->name('media.reorder');
    });
