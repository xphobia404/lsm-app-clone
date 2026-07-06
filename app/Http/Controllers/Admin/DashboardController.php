<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Section;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── KPI Cards ──────────────────────────────────────────────────────────
        $totalUsers       = User::where('role', 'user')->count();
        $activeUsers      = User::where('role', 'user')->where('is_active', true)->count();
        $inactiveUsers    = $totalUsers - $activeUsers;
        $totalSections    = Section::count();
        $totalQuizzes     = Quiz::count();
        $totalAttempts    = QuizAttempt::count();
        $totalCourseTypes = CourseType::count();

        // ── Completed Users ────────────────────────────────────────────────────
        // User dianggap "lulus" jika completed SEMUA section di spesialisasi mereka
        $completedUsers = User::where('role', 'user')
            ->with(['courseTypes', 'progresses'])
            ->get()
            ->filter(function (User $user) {
                $courseTypeIds = $user->courseTypes->pluck('id');

                if ($courseTypeIds->isEmpty()) {
                    return false;
                }

                $sectionIds = Section::whereIn('course_type_id', $courseTypeIds)->pluck('id');

                if ($sectionIds->isEmpty()) {
                    return false;
                }

                $completedCount = $user->progresses
                    ->whereIn('section_id', $sectionIds->toArray())
                    ->where('status', 'completed')
                    ->count();

                return $completedCount >= $sectionIds->count();
            })
            ->values();

        // ── Recent Progress (8 terbaru) ────────────────────────────────────────
        $recentProgress = UserProgress::with(['user', 'section.courseType'])
            ->whereHas('user', fn ($q) => $q->where('role', 'user'))
            ->orderByDesc('updated_at')
            ->take(8)
            ->get();

        // ── Bar Chart: user completed per spesialisasi ─────────────────────────
        $courseTypes  = CourseType::orderBy('order')->get();
        $chartLabels  = $courseTypes->pluck('name');
        $chartData    = $courseTypes->map(function (CourseType $ct) {
            $sectionIds   = Section::where('course_type_id', $ct->id)->pluck('id');
            $sectionCount = $sectionIds->count();

            if ($sectionCount === 0) {
                return 0;
            }

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

        // ── Donut Chart: distribusi status progress ────────────────────────────
        $allProgress     = UserProgress::whereHas('user', fn ($q) => $q->where('role', 'user'))->get();
        $donutCompleted  = $allProgress->where('status', 'completed')->count();
        $donutInProgress = $allProgress->where('status', 'in_progress')->count();
        $donutNotStarted = $allProgress->where('status', 'not_started')->count();

        // ── Spesialisasi Stats (with user count) ───────────────────────────────
        $courseTypeStats = CourseType::withCount(['users', 'sections'])->orderBy('order')->get();

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
