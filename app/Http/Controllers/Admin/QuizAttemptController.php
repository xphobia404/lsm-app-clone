<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\Section;
use App\Services\QuizAttemptExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizAttemptController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 25);
        if (! in_array($perPage, [10, 15, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = QuizAttempt::with([
            'user:id,name,username,email',
            'section:id,title,passing_score',
            'section.learningSchemas:id,title',
        ]);

        $filters = $request->only(['search', 'section_id', 'status']);
        app(QuizAttemptExportService::class)->applyFilters($query, $filters);

        $attempts = $query->latest('attempted_at')->paginate($perPage)->withQueryString();

        $sections = Section::where('is_active', true)->orderBy('title')->get(['id', 'title']);

        // Overall stats
        $totalAttempts = QuizAttempt::count();
        $totalPassed = QuizAttempt::passed()->count();
        $totalFailed = $totalAttempts - $totalPassed;

        return view('admin.quiz-attempts.index', compact(
            'attempts',
            'sections',
            'perPage',
            'totalAttempts',
            'totalPassed',
            'totalFailed'
        ));
    }

    public function export(Request $request, QuizAttemptExportService $exportService): StreamedResponse
    {
        $section = null;
        if ($request->filled('section_id')) {
            $section = Section::find($request->section_id);
        }

        return $exportService->exportCsv($section, $request->only(['search', 'section_id', 'status']));
    }
}
