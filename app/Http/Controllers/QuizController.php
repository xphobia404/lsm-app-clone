<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Section;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // ── ADMIN ────────────────────────────────────────────────────────────

    public function index(Request $request, Section $section)
    {
        $quizzes = $section->quizzes()
            ->when($request->filled('search'), fn ($q) =>
                $q->where('question', 'like', "%{$request->search}%")
            )
            ->when($request->filled('status'), fn ($q) =>
                $q->where('is_active', $request->status === 'active')
            )
            ->orderBy('quiz_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.quizzes.index', compact('section', 'quizzes'));
    }

    public function create(Section $section)
    {
        return view('admin.quizzes.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        $validated = $request->validate([
            'question'       => 'required|string|max:1000',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'required|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d',
            'explanation'    => 'nullable|string|max:2000',
            'quiz_order'     => 'nullable|integer|min:0',
            'is_active'      => 'sometimes|boolean',
        ]);

        $this->ensureAnswerOptionFilled($validated);

        $validated['quiz_order'] = $validated['quiz_order']
            ?? ($section->quizzes()->max('quiz_order') + 1);
        $validated['is_active'] = $request->boolean('is_active');

        $section->quizzes()->create($validated);

        return redirect()
            ->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Quiz berhasil ditambahkan.');
    }

    public function show(Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);
        return view('admin.quizzes.show', compact('section', 'quiz'));
    }

    public function edit(Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);
        return view('admin.quizzes.edit', compact('section', 'quiz'));
    }

    public function update(Request $request, Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);

        $validated = $request->validate([
            'question'       => 'required|string|max:1000',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'required|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d',
            'explanation'    => 'nullable|string|max:2000',
            'quiz_order'     => 'nullable|integer|min:0',
            'is_active'      => 'sometimes|boolean',
        ]);

        $this->ensureAnswerOptionFilled($validated);
        $validated['is_active'] = $request->boolean('is_active');

        $quiz->update($validated);

        return redirect()
            ->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Quiz berhasil diperbarui.');
    }

    public function destroy(Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);
        $quiz->delete();

        return redirect()
            ->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Quiz berhasil dihapus.');
    }

    public function toggleActive(Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);
        $quiz->update(['is_active' => ! $quiz->is_active]);
        $label = $quiz->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Quiz berhasil {$label}.");
    }

    // ── USER-FACING ─────────────────────────────────────────────────

    /**
     * Halaman list quiz untuk user (sebelum dikerjakan).
     * Route: GET /app/sections/{section}/quizzes
     */
    public function userIndex(Section $section)
    {
        abort_if(! $section->is_active, 404);

        $quizzes = $section->quizzes()->active()->orderBy('quiz_order')->get();
        $user    = auth()->user();

        // Cek apakah user sudah pernah attempt
        $lastAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->latest('attempted_at')
            ->first();

        return view('user.quizzes.index', compact('section', 'quizzes', 'lastAttempt'));
    }

    /**
     * Halaman mengerjakan 1 soal quiz.
     * Route: GET /app/sections/{section}/quizzes/{quiz}
     */
    public function userShow(Section $section, Quiz $quiz)
    {
        abort_if(! $section->is_active || ! $quiz->is_active, 404);
        $this->authorizeQuiz($section, $quiz);

        $allQuizzes   = $section->quizzes()->active()->orderBy('quiz_order')->get(['id', 'quiz_order']);
        $currentIndex = $allQuizzes->search(fn ($q) => $q->id === $quiz->id);
        $prev = $currentIndex > 0 ? $allQuizzes[$currentIndex - 1] : null;
        $next = $currentIndex < $allQuizzes->count() - 1 ? $allQuizzes[$currentIndex + 1] : null;
        $total = $allQuizzes->count();

        return view('user.quizzes.show', compact('section', 'quiz', 'prev', 'next', 'currentIndex', 'total'));
    }

    /**
     * Submit jawaban semua quiz sekaligus (dari form).
     * Route: POST /app/sections/{section}/quizzes/submit
     */
    public function userSubmit(Request $request, Section $section)
    {
        abort_if(! $section->is_active, 404);

        $quizzes = $section->quizzes()->active()->get();
        $answers = $request->input('answers', []);

        $correctCount = 0;
        $results = [];
        foreach ($quizzes as $quiz) {
            $userAnswer = $answers[$quiz->id] ?? null;
            $isCorrect  = $userAnswer === $quiz->correct_answer;
            if ($isCorrect) $correctCount++;
            $results[$quiz->id] = [
                'user_answer'    => $userAnswer,
                'correct_answer' => $quiz->correct_answer,
                'is_correct'     => $isCorrect,
                'explanation'    => $quiz->explanation,
            ];
        }

        // Simpan attempt
        $attempt = QuizAttempt::create([
            'user_id'         => auth()->id(),
            'section_id'      => $section->id,
            'total_questions' => $quizzes->count(),
            'correct_answers' => $correctCount,
            'attempted_at'    => now(),
        ]);

        // Update progress jika lulus (>= 70%)
        $percentage = $quizzes->count() > 0
            ? round(($correctCount / $quizzes->count()) * 100)
            : 0;

        if ($percentage >= 70) {
            auth()->user()->progresses()
                ->where('section_id', $section->id)
                ->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return view('user.quizzes.result', compact(
            'section', 'quizzes', 'results', 'correctCount', 'percentage', 'attempt'
        ));
    }

    // ── PRIVATE ─────────────────────────────────────────────────────

    private function authorizeQuiz(Section $section, Quiz $quiz): void
    {
        abort_if($quiz->section_id !== $section->id, 404);
    }

    private function ensureAnswerOptionFilled(array $data): void
    {
        $answer    = $data['correct_answer'] ?? null;
        $optionKey = 'option_' . $answer;

        if (in_array($answer, ['c', 'd']) && empty($data[$optionKey])) {
            abort(422, "Jawaban benar '{$answer}' membutuhkan opsi '{$optionKey}' diisi.");
        }
    }
}
