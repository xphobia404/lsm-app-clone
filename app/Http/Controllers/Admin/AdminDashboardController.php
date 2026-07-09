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
        // ── User Stats ─────────────────────────────────────────────
        $totalUsers    = User::users()->count();
        $activeUsers   = User::users()->active()->count();
        $inactiveUsers = $totalUsers - $activeUsers;

        // ── Schema & Section Stats ───────────────────────────────
        $totalSchemas  = LearningSchema::count();
        $totalSections = Section::count();
        $totalQuizzes  = Quiz::count();
        $totalAttempts = QuizAttempt::count();

        // ── Progress Donut Chart Data ───────────────────────────
        $progressStats = UserProgress::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $donutCompleted  = $progressStats['completed'] ?? 0;
        $donutInProgress = $progressStats['in_progress'] ?? 0;
        $donutNotStarted = $progressStats['not_started'] ?? 0;

        // ── Learning Schema User Status Donut Chart Data ─────────
        $schemaUserStats = DB::table('learning_schema_user')
            ->select('status', DB::raw('count(*) as total'))
            ->whereIn('status', ['active', 'dropped', 'completed'])
            ->groupBy('status')
            ->pluck('total', 'status');

        $schemaUserActive    = $schemaUserStats['active'] ?? 0;
        $schemaUserDropped   = $schemaUserStats['dropped'] ?? 0;
        $schemaUserCompleted = $schemaUserStats['completed'] ?? 0;

        // ── Completed Users:
        // user menyelesaikan seluruh section aktif dari schema yang terhubung ke user
        $completedUsers = User::users()
            ->with([
                'learningSchemas' => function ($q) {
                    $q->with([
                        'sections' => fn ($sq) => $sq
                            ->where('sections.is_active', true)
                            ->select('sections.id')
                    ])->select('learning_schemas.id', 'title');
                },
                'progresses' => fn ($q) => $q
                    ->where('status', 'completed')
                    ->select('id', 'user_id', 'section_id', 'status'),
            ])
            ->get(['id', 'name', 'username'])
            ->filter(function ($user) {
                $schemaSectionIds = $user->learningSchemas
                    ->flatMap(fn ($schema) => $schema->sections->pluck('id'))
                    ->unique()
                    ->values();

                if ($schemaSectionIds->isEmpty()) {
                    return false;
                }

                $completedSectionIds = $user->progresses
                    ->pluck('section_id')
                    ->unique()
                    ->values();

                return $schemaSectionIds->diff($completedSectionIds)->isEmpty();
            })
            ->values();

        // ── Recent Progress Activity ────────────────────────────
        $recentProgress = UserProgress::with([
                'user:id,name,username',
                'section:id,title',
            ])
            ->whereHas('user', fn ($q) => $q->where('role', 'user'))
            ->latest('updated_at')
            ->limit(8)
            ->get();

        // ── Schema Stats (section count per schema via pivot) ─────────
        $schemaStats = LearningSchema::select('id', 'title')
            ->selectSub(
                DB::table('learning_schema_section')
                    ->selectRaw('count(*)')
                    ->whereColumn('learning_schema_section.learning_schema_id', 'learning_schemas.id'),
                'sections_count'
            )
            ->orderByDesc('sections_count')
            ->limit(6)
            ->get();

        // ── Bar Chart: completed per schema ──────────────────────
        $schemaCompletion = LearningSchema::with([
                'sections' => fn ($q) => $q
                    ->where('sections.is_active', true)
                    ->select('sections.id')
            ])
            ->active()
            ->limit(6)
            ->get(['id', 'title'])
            ->map(function ($schema) {
                $sectionIds   = $schema->sections->pluck('id')->unique()->values();
                $sectionCount = $sectionIds->count();

                if ($sectionCount === 0) {
                    return ['title' => $schema->title, 'count' => 0];
                }

                $count = User::users()
                    ->with([
                        'progresses' => fn ($q) => $q
                            ->where('status', 'completed')
                            ->whereIn('section_id', $sectionIds)
                            ->select('id', 'user_id', 'section_id', 'status')
                    ])
                    ->get(['id'])
                    ->filter(function ($user) use ($sectionIds) {
                        $completedSectionIds = $user->progresses
                            ->pluck('section_id')
                            ->unique()
                            ->values();

                        return $sectionIds->diff($completedSectionIds)->isEmpty();
                    })
                    ->count();

                return ['title' => $schema->title, 'count' => $count];
            });

        $chartLabels = $schemaCompletion->pluck('title');
        $chartData   = $schemaCompletion->pluck('count');

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalSchemas',
            'totalSections',
            'totalQuizzes',
            'totalAttempts',
            'donutCompleted',
            'donutInProgress',
            'donutNotStarted',
            'schemaUserActive',
            'schemaUserDropped',
            'schemaUserCompleted',
            'completedUsers',
            'recentProgress',
            'schemaStats',
            'chartLabels',
            'chartData',
        ));
    }
}