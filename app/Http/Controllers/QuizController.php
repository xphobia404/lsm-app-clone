<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    public function index(Request $request, Section $section): JsonResponse
    {
        $quizzes = $section->quizzes()
            ->when($request->filled('is_active'), fn ($q) =>
                $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN))
            )
            ->ordered()
            ->paginate($request->get('per_page', 15));

        return response()->json($quizzes);
    }

    public function store(Request $request, Section $section): JsonResponse
    {
        $validated = $request->validate([
            'question'       => 'required|string',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'required|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d',
            'explanation'    => 'nullable|string',
            'quiz_order'     => 'sometimes|integer|min:0',
            'is_active'      => 'sometimes|boolean',
        ]);

        $this->validateCorrectAnswerOption($validated);

        if (!isset($validated['quiz_order'])) {
            $validated['quiz_order'] = $section->quizzes()->max('quiz_order') + 1;
        }

        $quiz = $section->quizzes()->create($validated);

        return response()->json([
            'message' => 'Quiz created successfully.',
            'data'    => $quiz,
        ], 201);
    }

    public function show(Section $section, Quiz $quiz): JsonResponse
    {
        $this->authorizeQuiz($section, $quiz);

        return response()->json($quiz);
    }

    public function update(Request $request, Section $section, Quiz $quiz): JsonResponse
    {
        $this->authorizeQuiz($section, $quiz);

        $validated = $request->validate([
            'question'       => 'sometimes|string',
            'option_a'       => 'sometimes|string|max:255',
            'option_b'       => 'sometimes|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => 'sometimes|in:a,b,c,d',
            'explanation'    => 'nullable|string',
            'quiz_order'     => 'sometimes|integer|min:0',
            'is_active'      => 'sometimes|boolean',
        ]);

        $this->validateCorrectAnswerOption(array_merge($quiz->toArray(), $validated));

        $quiz->update($validated);

        return response()->json([
            'message' => 'Quiz updated successfully.',
            'data'    => $quiz->fresh(),
        ]);
    }

    public function destroy(Section $section, Quiz $quiz): JsonResponse
    {
        $this->authorizeQuiz($section, $quiz);

        $quiz->delete();

        return response()->json(['message' => 'Quiz deleted successfully.']);
    }

    private function authorizeQuiz(Section $section, Quiz $quiz): void
    {
        abort_if($quiz->section_id !== $section->id, 404);
    }

    private function validateCorrectAnswerOption(array $data): void
    {
        $answer = $data['correct_answer'] ?? null;
        $optionKey = 'option_' . $answer;

        if (in_array($answer, ['c', 'd']) && empty($data[$optionKey])) {
            abort(422, "correct_answer '{$answer}' requires '{$optionKey}' to be filled.");
        }
    }
}
