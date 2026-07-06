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

        // ── Completed Users (optimized: hindari N+1) ───────────────────────────
        // Ambil semua user sekaligus dengan relasi yang dibutuhkan
        $allRegularUsers = User::where('role', 'user')
            ->with(['courseTypes:id', 'progresses:user_id,section_id,status'])
            ->get();

        // Ambil semua section ids per course_type (1 query)
        $sectionIdsByCourseType = Section::select('id', 'course_type_id')
            ->get()
            ->groupBy('course_type_id')
            ->map(fn ($s) => $s->pluck('id'));

        $completedUsers = $allRegularUsers->filter(function (User $user) use ($sectionIdsByCourseType) {
            $courseTypeIds = $user->courseTypes->pluck('id');

            if ($courseTypeIds->isEmpty()) {
                return false;
            }

            // Kumpulkan semua section ids milik user
            $userSectionIds = $courseTypeIds
                ->flatMap(fn ($ctId) => $sectionIdsByCourseType->get($ctId, collect()))
                ->unique();

            if ($userSectionIds->isEmpty()) {
                return false;
            }

            $completedCount = $user->progresses
                ->whereIn('section_id', $userSectionIds->toArray())
                ->where('status', 'completed')
                ->count();

            return $completedCount >= $userSectionIds->count();
        })->values();

        // ── Recent Progress (8 terbaru) ────────────────────────────────────────
        $recentProgress = UserProgress::with(['user:id,name,username', 'section:id,title,course_type_id', 'section.courseType:id,name'])
            ->whereHas('user', fn ($q) => $q->where('role', 'user'))
            ->orderByDesc('updated_at')
            ->take(8)
            ->get();

        // ── Bar Chart: user completed per spesialisasi ─────────────────────────
        $courseTypes = CourseType::orderBy('order')->get();
        $chartLabels = $courseTypes->pluck('name');
        $chartData   = $courseTypes->map(function (CourseType $ct) {
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
