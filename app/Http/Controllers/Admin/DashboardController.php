<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Models\User;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers    = User::where('role', 'user')->count();
        $totalSections = Section::count();
        $totalQuizzes  = Quiz::count();
        $activeUsers   = User::where('role', 'user')->where('is_active', true)->count();
        $inactiveUsers = $totalUsers - $activeUsers;
        $totalAttempts = QuizAttempt::count();
        $totalCourseTypes = CourseType::count();

        // Users yang sudah lulus SEMUA section dari spesialisasi yang mereka ikuti
        $completedUsers = User::where('role', 'user')
            ->with(['courseTypes', 'progress'])
            ->get()
            ->filter(function ($user) {
                // Ambil semua spesialisasi user
                $courseTypes = $user->courseTypes;

                // Jika user belum terdaftar di spesialisasi apapun, belum lulus
                if ($courseTypes->isEmpty()) return false;

                // Ambil semua section_id dari spesialisasi yang diikuti user
                $sectionIds = Section::whereIn('course_type_id', $courseTypes->pluck('id'))->pluck('id');

                if ($sectionIds->isEmpty()) return false;

                // Hitung berapa section yang sudah completed oleh user ini
                $completedCount = $user->progress
                    ->whereIn('section_id', $sectionIds->toArray())
                    ->where('status', 'completed')
                    ->count();

                return $completedCount >= $sectionIds->count();
            })
            ->values();

        // 8 user dengan progress terbaru
        $recentProgress = UserProgress::with(['user', 'section'])
            ->whereHas('user', fn($q) => $q->where('role', 'user'))
            ->orderByDesc('updated_at')
            ->take(8)
            ->get();

        // Chart data: jumlah user yang sudah completed SEMUA section dalam 1 spesialisasi
        $courseTypes = CourseType::orderBy('order')->get();

        $chartLabels = $courseTypes->pluck('name');
        $chartData   = $courseTypes->map(function ($ct) {
            $sectionIds   = Section::where('course_type_id', $ct->id)->pluck('id');
            $sectionCount = $sectionIds->count();

            if ($sectionCount === 0) return 0;

            // Subquery PostgreSQL-safe: ambil user_id yang completed >= sectionCount
            $completedUserIds = DB::table('user_progress')
                ->select('user_id')
                ->whereIn('section_id', $sectionIds)
                ->where('status', 'completed')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) >= ?', [$sectionCount])
                ->pluck('user_id');

            return User::where('role', 'user')
                ->whereIn('id', $completedUserIds)
                ->count();
        });

        // Donut: completed vs in_progress vs not_started
        $allProgress      = UserProgress::whereHas('user', fn($q) => $q->where('role', 'user'))->get();
        $donutCompleted   = $allProgress->where('status', 'completed')->count();
        $donutInProgress  = $allProgress->where('status', 'in_progress')->count();
        $donutNotStarted  = $allProgress->where('status', 'not_started')->count();

        // Spesialisasi stats
        $courseTypeStats = CourseType::withCount('users')->orderBy('order')->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalSections', 'totalQuizzes',
            'totalAttempts', 'activeUsers', 'inactiveUsers',
            'totalCourseTypes', 'completedUsers', 'recentProgress',
            'chartLabels', 'chartData',
            'donutCompleted', 'donutInProgress', 'donutNotStarted',
            'courseTypeStats'
        ));
    }
}
