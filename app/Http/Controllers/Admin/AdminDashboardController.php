<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningSchema;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Section;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        // ── User Stats ─────────────────────────────────────────────────
        $totalUsers    = User::users()->count();
        $activeUsers   = User::users()->active()->count();
        $inactiveUsers = $totalUsers - $activeUsers;

        // ── Schema & Section Stats ─────────────────────────────────────
        $totalSchemas  = LearningSchema::count();
        $totalSections = Section::count();
        $totalQuizzes  = Quiz::count();
        $totalAttempts = QuizAttempt::count();

        // ── Progress Donut Chart Data ─────────────────────────────────
        $progressStats   = UserProgress::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $donutCompleted  = $progressStats['completed']   ?? 0;
        $donutInProgress = $progressStats['in_progress'] ?? 0;
        $donutNotStarted = $progressStats['not_started'] ?? 0;

        // ── Completed Users (selesai SEMUA section aktif) ────────────────
        $totalActiveSections = Section::where('is_active', true)->count();

        $completedUsers = $totalActiveSections > 0
            ? User::users()
                ->whereHas('progresses', fn ($q) =>
                    $q->where('status', 'completed'),
                    '>=',
                    $totalActiveSections
                )
                ->get(['id', 'name', 'username'])
            : collect();

        // ── Recent Progress Activity ────────────────────────────────
        $recentProgress = UserProgress::with([
            'user:id,name,username',
            'section:id,title',
        ])
        ->whereHas('user', fn ($q) => $q->where('role', 'user'))
        ->latest('updated_at')
        ->limit(8)
        ->get();

        // ── Schema Stats (section count per schema) ───────────────────
        $schemaStats = LearningSchema::withCount('sections')
            ->orderByDesc('sections_count')
            ->limit(6)
            ->get(['id', 'title']);

        // ── Bar Chart: completed per schema ──────────────────────────
        // Hitung berapa user yang sudah complete semua section di tiap schema
        $schemaCompletion = LearningSchema::with(['sections:id'])
            ->active()
            ->limit(6)
            ->get(['id', 'title'])
            ->map(function ($schema) {
                $sectionIds  = $schema->sections->pluck('id');
                $sectionCount = $sectionIds->count();

                if ($sectionCount === 0) {
                    return ['title' => $schema->title, 'count' => 0];
                }

                $count = User::users()
                    ->whereHas('progresses', fn ($q) =>
                        $q->whereIn('section_id', $sectionIds)
                          ->where('status', 'completed'),
                        '>=',
                        $sectionCount
                    )
                    ->count();

                return ['title' => $schema->title, 'count' => $count];
            });

        $chartLabels = $schemaCompletion->pluck('title');
        $chartData   = $schemaCompletion->pluck('count');

        return view('admin.dashboard', compact(
            // User
            'totalUsers', 'activeUsers', 'inactiveUsers',
            // Schema / Section / Quiz
            'totalSchemas', 'totalSections', 'totalQuizzes', 'totalAttempts',
            // Donut
            'donutCompleted', 'donutInProgress', 'donutNotStarted',
            // Completed users
            'completedUsers',
            // Recent activity
            'recentProgress',
            // Schema stats bar
            'schemaStats',
            // Bar chart
            'chartLabels', 'chartData',
        ));
    }
}
