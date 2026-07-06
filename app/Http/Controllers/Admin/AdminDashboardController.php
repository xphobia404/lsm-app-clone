<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningSchema;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserProgress;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        // ── Users ──────────────────────────────────────────────────────
        $totalUsers    = User::where('role', 'user')->count();
        $activeUsers   = User::where('role', 'user')->where('is_active', true)->count();
        $inactiveUsers = User::where('role', 'user')->where('is_active', false)->count();

        // ── Content ────────────────────────────────────────────────────
        $totalSchemas  = LearningSchema::where('is_active', true)->count();
        $totalSections = Section::count();
        $totalQuizzes  = Quiz::count();
        $totalAttempts = QuizAttempt::count();

        // ── Completed Users (selesai semua section) ────────────────────
        $allSectionIds     = Section::pluck('id');
        $totalSectionCount = $allSectionIds->count();

        if ($totalSectionCount > 0) {
            $completedUsers = User::where('role', 'user')
                ->whereHas('progresses', function ($q) use ($totalSectionCount) {
                    $q->where('status', 'completed');
                }, '>=', $totalSectionCount)
                ->get();
        } else {
            $completedUsers = collect();
        }

        // ── Recent Progress ────────────────────────────────────────────
        $recentProgress = UserProgress::with(['user', 'section'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        // ── Donut Chart: status progress keseluruhan ───────────────────
        $donutCompleted  = UserProgress::where('status', 'completed')->count();
        $donutInProgress = UserProgress::where('status', 'in_progress')->count();
        $donutNotStarted = UserProgress::where('status', 'not_started')->count();

        // ── Bar Chart & Schema Stats ───────────────────────────────────
        $schemaStats = LearningSchema::withCount('sections')
            ->where('is_active', true)
            ->orderByDesc('sections_count')
            ->get();

        // Bar chart: jumlah user yang selesai per schema
        $schemas = LearningSchema::where('is_active', true)
            ->with(['sections'])
            ->get();

        $chartLabels = collect();
        $chartData   = collect();

        foreach ($schemas as $schema) {
            $sectionIds = $schema->sections->pluck('id');
            if ($sectionIds->isEmpty()) continue;

            $completed = User::where('role', 'user')
                ->whereHas('progresses', function ($q) use ($sectionIds) {
                    $q->whereIn('section_id', $sectionIds)
                      ->where('status', 'completed');
                }, '>=', $sectionIds->count())
                ->count();

            $chartLabels->push(\Str::limit($schema->title, 20));
            $chartData->push($completed);
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalSchemas',
            'totalSections',
            'totalQuizzes',
            'totalAttempts',
            'completedUsers',
            'recentProgress',
            'donutCompleted',
            'donutInProgress',
            'donutNotStarted',
            'schemaStats',
            'chartLabels',
            'chartData'
        ));
    }
}
