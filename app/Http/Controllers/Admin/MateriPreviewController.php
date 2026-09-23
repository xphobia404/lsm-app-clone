<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningSchema;
use App\Models\Section;
use App\Support\MateriPreviewContext;
use Illuminate\Http\Request;

class MateriPreviewController extends Controller
{
    public function show(LearningSchema $learningSchema)
    {
        $learningSchema->load([
            'sections' => fn ($q) => $q->where('sections.is_active', true)
                ->withCount(['contents', 'quizzes']),
        ]);

        $progressMap = collect();

        return view('user.schemas.show', array_merge([
            'learningSchema' => $learningSchema,
            'progressMap' => $progressMap,
            'adminPreview' => true,
        ], MateriPreviewContext::variables($learningSchema, true)));
    }

    public function section(LearningSchema $learningSchema, Section $section)
    {
        $this->ensureSectionBelongsToSchema($learningSchema, $section);

        $allSections = $learningSchema->sections()
            ->where('is_active', true)
            ->orderBy('learning_schema_section.section_order')
            ->get(['sections.id', 'sections.title']);

        $currentIndex = $allSections->search(fn ($s) => $s->id === $section->id);
        abort_if($currentIndex === false, 404);

        $section->load([
            'contents' => function ($q) {
                $q->active()->ordered()
                    ->with(['media' => fn ($m) => $m->where('is_active', true)->orderBy('media_order')]);
            },
            'quizzes' => fn ($q) => $q->where('is_active', true),
        ]);

        $prevSection = $currentIndex > 0 ? $allSections[$currentIndex - 1] : null;
        $nextSection = $currentIndex < $allSections->count() - 1 ? $allSections[$currentIndex + 1] : null;

        return view('user.section', array_merge([
            'learningSchema' => $learningSchema,
            'section' => $section,
            'prevSection' => $prevSection,
            'nextSection' => $nextSection,
            'adminPreview' => true,
        ], MateriPreviewContext::variables($learningSchema, true)));
    }

    public function quizzes(LearningSchema $learningSchema, Section $section)
    {
        $this->ensureSectionBelongsToSchema($learningSchema, $section);

        $quizzes = $section->quizzes()
            ->active()
            ->with(['activeMedia' => fn ($q) => $q->where('media_type', 'image')])
            ->orderBy('quiz_order')
            ->get();

        return view('user.quizzes.index', array_merge([
            'section' => $section,
            'quizzes' => $quizzes,
            'lastAttempt' => null,
            'learningSchema' => $learningSchema,
            'adminPreview' => true,
        ], MateriPreviewContext::variables($learningSchema, true)));
    }

    public function submitQuiz(Request $request, LearningSchema $learningSchema, Section $section)
    {
        $this->ensureSectionBelongsToSchema($learningSchema, $section);

        $quizzes = $section->quizzes()->active()->orderBy('quiz_order')->get();
        $answers = $request->input('answers', []);

        $correctCount = 0;
        $results = [];
        foreach ($quizzes as $quiz) {
            $userAnswer = $answers[$quiz->id] ?? null;
            $isCorrect = $userAnswer === $quiz->correct_answer;
            if ($isCorrect) {
                $correctCount++;
            }
            $results[$quiz->id] = [
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
            ];
        }

        $passed = $quizzes->isNotEmpty() && $correctCount === $quizzes->count();

        return view('user.quizzes.result', array_merge([
            'section' => $section,
            'quizzes' => $quizzes,
            'results' => $results,
            'correctCount' => $correctCount,
            'passed' => $passed,
            'learningSchema' => $learningSchema,
            'adminPreview' => true,
        ], MateriPreviewContext::variables($learningSchema, true)));
    }

    private function ensureSectionBelongsToSchema(LearningSchema $learningSchema, Section $section): void
    {
        abort_unless(
            $learningSchema->sections()->where('sections.id', $section->id)->exists(),
            404
        );
    }
}
