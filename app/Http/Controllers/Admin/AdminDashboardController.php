<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningSchema;
use App\Models\QuizAttempt;
use App\Models\Section;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        // ── Summary Cards ──────────────────────────────────────────────
        $totalUsers        = User::where('role', 'user')->count();
        $activeUsers       = User::where('role', 'user')->where('is_active', true)->count();
        $totalSchemas      = LearningSchema::count();
        $activeSchemas     = LearningSchema::where('is_active', true)->count();
        $totalSections     = Section::count();
        $activeSections    = Section::where('is_active', true)->count();
        $totalAttempts     = QuizAttempt::count();
        $completedProgress = UserProgress::where('status', 'completed')->count();

        // ── Progress Overview ──────────────────────────────────────────
        $progressStats = UserProgress::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── Top Sections (paling banyak progress completed) ─────────
        $topSections = Section::withCount([
            'progresses as completed_count' => fn ($q) =>
                $q->where('status', 'completed'),
        ])
        ->orderByDesc('completed_count')
        ->limit(5)
        ->get(['id', 'title']);

        // ── Recent Quiz Attempts ──────────────────────────────────────
        $recentAttempts = QuizAttempt::with([
            'user:id,name,username',
            'section:id,title',
        ])
        ->latest('attempted_at')
        ->limit(8)
        ->get();

        // ── Registrasi user baru (7 hari terakhir) ────────────────
        $newUsersThisWeek = User::where('role', 'user')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // ── Rata-rata score quiz ─────────────────────────────────────
        $avgScore = QuizAttempt::whereColumn('total_questions', '>', DB::raw('0'))
            ->selectRaw('ROUND(AVG(correct_answers * 100.0 / total_questions)) as avg_pct')
            ->value('avg_pct') ?? 0;

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers',
            'totalSchemas', 'activeSchemas',
            'totalSections', 'activeSections',
            'totalAttempts', 'completedProgress',
            'progressStats', 'topSections',
            'recentAttempts', 'newUsersThisWeek', 'avgScore'
        ));
    }
}
