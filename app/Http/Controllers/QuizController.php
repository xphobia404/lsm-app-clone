<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    // ── ADMIN ────────────────────────────────────────────────────────────────────────────

    public function index(Request $request, Section $section)
    {
        $perPage = (int) $request->input('per_page', 25);

        if (! in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 25;
        }

        $quizzes = $section->quizzes()
            ->with('media')
            ->withCount('media')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($q2) use ($search) {
                    $q2->where('question', 'like', $search)
                    ->orWhere('explanation', 'like', $search)
                    ->orWhere('option_a', 'like', $search)
                    ->orWhere('option_b', 'like', $search)
                    ->orWhere('option_c', 'like', $search)
                    ->orWhere('option_d', 'like', $search);
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('is_active', $request->status === 'active');
            })
            ->orderBy('quiz_order')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.quizzes.index', compact('section', 'quizzes', 'perPage'));
    }

    public function create(Section $section)
    {
        return view('admin.quizzes.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        $validated = $request->validate([
            'question'          => 'required|string|max:1000',
            'option_a'          => 'required|string|max:255',
            'option_b'          => 'required|string|max:255',
            'option_c'          => 'nullable|string|max:255',
            'option_d'          => 'nullable|string|max:255',
            'correct_answer'    => 'required|in:a,b,c,d',
            'explanation'       => 'nullable|string|max:2000',
            'quiz_order'        => 'nullable|integer|min:0',
            'is_active'         => 'sometimes|boolean',
            'media'             => 'nullable|array|max:5',
            'media.*.media_type'  => 'required_with:media|in:image,video,audio,url',
            'media.*.title'       => 'nullable|string|max:255',
            'media.*.description' => 'nullable|string|max:500',
            'media.*.url'         => 'nullable|string|max:2000',
            'media.*.file'        => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,wav|max:20480',
            'media.*.media_order' => 'nullable|integer|min:0',
        ]);

        $this->ensureAnswerOptionFilled($validated);

        $validated['quiz_order'] = $validated['quiz_order']
            ?? ($section->quizzes()->max('quiz_order') + 1);
        $validated['is_active'] = $request->boolean('is_active');

        $quiz = $section->quizzes()->create($validated);

        $this->syncMedia($request, $quiz);

        return redirect()
            ->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Quiz berhasil ditambahkan.');
    }

    public function show(Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);
        $quiz->load('media');
        return view('admin.quizzes.show', compact('section', 'quiz'));
    }

    public function edit(Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);
        $quiz->load('media');
        return view('admin.quizzes.edit', compact('section', 'quiz'));
    }

    public function update(Request $request, Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);

        $validated = $request->validate([
            'question'          => 'required|string|max:1000',
            'option_a'          => 'required|string|max:255',
            'option_b'          => 'required|string|max:255',
            'option_c'          => 'nullable|string|max:255',
            'option_d'          => 'nullable|string|max:255',
            'correct_answer'    => 'required|in:a,b,c,d',
            'explanation'       => 'nullable|string|max:2000',
            'quiz_order'        => 'nullable|integer|min:0',
            'is_active'         => 'sometimes|boolean',
            'media'             => 'nullable|array|max:5',
            'media.*.media_type'  => 'required_with:media|in:image,video,audio,url',
            'media.*.title'       => 'nullable|string|max:255',
            'media.*.description' => 'nullable|string|max:500',
            'media.*.url'         => 'nullable|string|max:2000',
            'media.*.file'        => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,wav|max:20480',
            'media.*.media_order' => 'nullable|integer|min:0',
            'delete_media'      => 'nullable|array',
            'delete_media.*'    => 'integer|exists:media,id',
        ]);

        $this->ensureAnswerOptionFilled($validated);
        $validated['is_active'] = $request->boolean('is_active');

        $quiz->update($validated);

        if (!empty($validated['delete_media'])) {
            $toDelete = $quiz->media()->whereIn('id', $validated['delete_media'])->get();
            foreach ($toDelete as $m) {
                if ($m->file_path) Storage::delete($m->file_path);
                $m->delete();
            }
        }

        $this->syncMedia($request, $quiz);

        return redirect()
            ->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Quiz berhasil diperbarui.');
    }

    public function destroy(Section $section, Quiz $quiz)
    {
        $this->authorizeQuiz($section, $quiz);

        foreach ($quiz->media as $m) {
            if ($m->file_path) Storage::delete($m->file_path);
        }
        $quiz->media()->delete();
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

    // ── USER-FACING ────────────────────────────────────────────────────────────────────────────

    public function userIndex(Section $section)
    {
        abort_if(! $section->is_active, 404);

        $quizzes = $section->quizzes()
            ->active()
            ->with(['activeMedia' => fn ($q) => $q->where('media_type', 'image')])
            ->orderBy('quiz_order')
            ->get();

        $user = auth()->user();

        $lastAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->latest('attempted_at')
            ->first();

        $learningSchema = $section->learningSchemas()->first();

        return view('user.quizzes.index', compact('section', 'quizzes', 'lastAttempt', 'learningSchema'));
    }

    public function userShow(Section $section, Quiz $quiz)
    {
        abort_if(! $section->is_active || ! $quiz->is_active, 404);
        $this->authorizeQuiz($section, $quiz);

        $quiz->load('activeMedia');

        $allQuizzes   = $section->quizzes()->active()->orderBy('quiz_order')->get(['id', 'quiz_order']);
        $currentIndex = $allQuizzes->search(fn ($q) => $q->id === $quiz->id);
        $prev  = $currentIndex > 0 ? $allQuizzes[$currentIndex - 1] : null;
        $next  = $currentIndex < $allQuizzes->count() - 1 ? $allQuizzes[$currentIndex + 1] : null;
        $total = $allQuizzes->count();

        $learningSchema = $section->learningSchemas()->first();

        return view('user.quizzes.show', compact('section', 'quiz', 'prev', 'next', 'currentIndex', 'total', 'learningSchema'));
    }

    public function userSubmit(Request $request, Section $section)
    {
        abort_if(! $section->is_active, 404);

        $quizzes = $section->quizzes()->active()->orderBy('quiz_order')->get();
        $answers = $request->input('answers', []);

        $correctCount = 0;
        $results = [];
        foreach ($quizzes as $quiz) {
            $userAnswer = $answers[$quiz->id] ?? null;
            $isCorrect  = $userAnswer === $quiz->correct_answer;
            if ($isCorrect) $correctCount++;
            $results[$quiz->id] = [
                'user_answer' => $userAnswer,
                'is_correct'  => $isCorrect,
            ];
        }

        $passed = $correctCount === $quizzes->count();

        QuizAttempt::create([
            'user_id'         => auth()->id(),
            'section_id'      => $section->id,
            'total_questions' => $quizzes->count(),
            'correct_answers' => $correctCount,
            'attempted_at'    => now(),
        ]);

        if ($passed) {
            auth()->user()->progresses()
                ->where('section_id', $section->id)
                ->update(['status' => 'completed', 'completed_at' => now()]);
        }

        $learningSchema = $section->learningSchemas()->first();

        return view('user.quizzes.result', compact(
            'section', 'quizzes', 'results', 'correctCount', 'passed', 'learningSchema'
        ));
    }

    // ── PRIVATE ────────────────────────────────────────────────────────────────────────────

    private function syncMedia(Request $request, Quiz $quiz): void
    {
        $mediaInputs = $request->input('media', []);
        $files       = $request->file('media', []);

        foreach ($mediaInputs as $i => $item) {
            $mediaType = $item['media_type'] ?? null;
            if (! $mediaType) continue;

            $filePath = null;
            if (isset($files[$i]['file'])) {
                $filePath = $files[$i]['file']->store('quizzes/media', 'public');
            }

            $quiz->media()->create([
                'media_type'  => $mediaType,
                'title'       => $item['title']       ?? null,
                'description' => $item['description'] ?? null,
                'url'         => $item['url']         ?? null,
                'file_path'   => $filePath,
                'media_order' => $item['media_order'] ?? ($i + 1),
                'is_active'   => true,
            ]);
        }
    }

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
