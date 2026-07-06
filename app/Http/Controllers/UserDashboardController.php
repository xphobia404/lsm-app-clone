<?php

namespace App\Http\Controllers;

use App\Models\UserProgress;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Hanya schema yang di-enroll admin ke user ini
        $schemas = $user->learningSchemas()
            ->with(['sections' => fn ($q) => $q->where('is_active', true)])
            ->get();

        // Semua progress user
        $allProgress = UserProgress::forUser($user->id)
            ->with('section:id,title')
            ->get();

        $totalSections   = $allProgress->count();
        $completedCount  = $allProgress->where('status', 'completed')->count();
        $inProgressCount = $allProgress->where('status', 'in_progress')->count();
        $overallPct      = $totalSections > 0
            ? (int) round(($completedCount / $totalSections) * 100)
            : 0;

        // Quiz attempts
        $quizAttempts = $user->quizAttempts()->latest('attempted_at')->get();
        $quizTotal    = $quizAttempts->count();
        $quizPassed   = $quizAttempts->filter(fn ($a) => $a->isPassed())->count();
        $avgScore     = $quizTotal > 0
            ? (int) round($quizAttempts->avg(fn ($a) => $a->getScorePercentage()))
            : 0;

        $progressMap = $allProgress->pluck('status', 'section_id');

        $schemaStats = $schemas->map(function ($schema) use ($progressMap) {
            $sectionIds = $schema->sections->pluck('id');
            $total      = $sectionIds->count();
            $done       = $sectionIds->filter(fn ($id) => $progressMap->get($id) === 'completed')->count();
            $ongoing    = $sectionIds->filter(fn ($id) => $progressMap->get($id) === 'in_progress')->count();
            $pct        = $total > 0 ? (int) round(($done / $total) * 100) : 0;

            return [
                'schema'       => $schema,
                'total'        => $total,
                'done'         => $done,
                'ongoing'      => $ongoing,
                'pct'          => $pct,
                'enrollStatus' => $schema->pivot->status ?? 'active',
            ];
        });

        // Section yang sedang in_progress
        $continueSections = $allProgress
            ->where('status', 'in_progress')
            ->sortByDesc('updated_at')
            ->take(3);

        return view('user.dashboard', compact(
            'totalSections', 'completedCount', 'inProgressCount', 'overallPct',
            'quizTotal', 'quizPassed', 'avgScore',
            'schemaStats', 'continueSections'
        ));
    }
}
