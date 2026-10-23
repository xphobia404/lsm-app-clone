<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Section;
use App\Models\UserProgress;
use App\Services\QuizAttemptExportService;
use App\Services\QuizCsvImportService;
use App\Support\MateriPreviewContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                $search = '%'.$request->search.'%';
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

        $attemptsCount = QuizAttempt::where('section_id', $section->id)->count();

        return view('admin.quizzes.index', compact('section', 'quizzes', 'perPage', 'attemptsCount'));
    }

    public function downloadTemplate(QuizCsvImportService $importService): StreamedResponse
    {
        return response()->streamDownload(function () use ($importService) {
            echo $importService->generateTemplateContent();
        }, 'template_quiz.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importCsv(Request $request, Section $section, QuizCsvImportService $importService)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:4096',
        ], [
            'csv_file.required' => 'File CSV wajib diunggah.',
            'csv_file.mimes' => 'File harus berformat CSV atau TXT.',
            'csv_file.max' => 'Ukuran file maksimal 4MB.',
        ]);

        $result = $importService->import($request->file('csv_file'), $section);

        $successCount = $result['success_count'];
        $errors = $result['errors'];

        $redirect = redirect()->route('admin.sections.quizzes.index', $section);

        if ($successCount > 0 && empty($errors)) {
            return $redirect->with('success', "Berhasil mengimpor {$successCount} soal quiz.");
        }

        if ($successCount > 0 && ! empty($errors)) {
            return $redirect
                ->with('success', "Berhasil mengimpor {$successCount} soal quiz.")
                ->with('import_errors', $errors);
        }

        return $redirect
            ->with('error', 'Gagal mengimpor soal quiz. Silakan periksa kembali file CSV Anda.')
            ->with('import_errors', $errors);
    }

    public function updateSettings(Request $request, Section $section)
    {
        $validated = $request->validate([
            'passing_score' => 'required|integer|min:0|max:100',
            'randomize_questions' => 'sometimes|boolean',
            'randomize_options' => 'sometimes|boolean',
        ], [
            'passing_score.required' => 'Minimal skor wajib diisi.',
            'passing_score.integer' => 'Minimal skor harus berupa angka bulat.',
            'passing_score.min' => 'Minimal skor minimal 0%.',
            'passing_score.max' => 'Minimal skor maksimal 100%.',
        ]);

        $section->update([
            'passing_score' => $validated['passing_score'],
            'randomize_questions' => $request->boolean('randomize_questions'),
            'randomize_options' => $request->boolean('randomize_options'),
        ]);

        return back()->with('success', 'Pengaturan kuis berhasil disimpan.');
    }

    public function exportAttempts(Section $section, QuizAttemptExportService $exportService): StreamedResponse
    {
        return $exportService->exportCsv($section);
    }

    public function create(Section $section)
    {
        return view('admin.quizzes.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d',
            'explanation' => 'nullable|string|max:2000',
            'quiz_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'media' => 'nullable|array|max:5',
            'media.*.media_type' => 'required_with:media|in:image,video,audio,url',
            'media.*.title' => 'nullable|string|max:255',
            'media.*.description' => 'nullable|string|max:500',
            'media.*.url' => 'nullable|string|max:2000',
            'media.*.file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,wav|max:20480',
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
            'question' => 'required|string|max:1000',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d',
            'explanation' => 'nullable|string|max:2000',
            'quiz_order' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'media' => 'nullable|array|max:5',
            'media.*.media_type' => 'required_with:media|in:image,video,audio,url',
            'media.*.title' => 'nullable|string|max:255',
            'media.*.description' => 'nullable|string|max:500',
            'media.*.url' => 'nullable|string|max:2000',
            'media.*.file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mp3,wav|max:20480',
            'media.*.media_order' => 'nullable|integer|min:0',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'integer|exists:media,id',
        ]);

        $this->ensureAnswerOptionFilled($validated);
        $validated['is_active'] = $request->boolean('is_active');

        $quiz->update($validated);

        if (! empty($validated['delete_media'])) {
            $toDelete = $quiz->media()->whereIn('id', $validated['delete_media'])->get();
            foreach ($toDelete as $m) {
                if ($m->file_path) {
                    Storage::delete($m->file_path);
                }
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
            if ($m->file_path) {
                Storage::delete($m->file_path);
            }
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

    public function userIndex(Request $request, Section $section)
    {
        abort_if(! $section->is_active, 404);

        $quizzesQuery = $section->quizzes()
            ->active()
            ->with(['activeMedia' => fn ($q) => $q->where('media_type', 'image')]);

        $quizzes = $section->randomize_questions
            ? $quizzesQuery->inRandomOrder()->get()
            : $quizzesQuery->orderBy('quiz_order')->get();

        $user = auth()->user();

        $lastAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('section_id', $section->id)
            ->latest('attempted_at')
            ->first();

        $learningSchema = $this->resolveLearningSchema($section, $request);

        return view('user.quizzes.index', array_merge(
            compact('section', 'quizzes', 'lastAttempt', 'learningSchema'),
            MateriPreviewContext::variables($learningSchema, false),
        ));
    }

    public function userShow(Request $request, Section $section, Quiz $quiz)
    {
        abort_if(! $section->is_active || ! $quiz->is_active, 404);
        $this->authorizeQuiz($section, $quiz);

        $quiz->load('activeMedia');

        $allQuizzes = $section->quizzes()->active()->orderBy('quiz_order')->get(['id', 'quiz_order']);
        $currentIndex = $allQuizzes->search(fn ($q) => $q->id === $quiz->id);
        $prev = $currentIndex > 0 ? $allQuizzes[$currentIndex - 1] : null;
        $next = $currentIndex < $allQuizzes->count() - 1 ? $allQuizzes[$currentIndex + 1] : null;
        $total = $allQuizzes->count();

        $learningSchema = $this->resolveLearningSchema($section, $request);

        return view('user.quizzes.show', compact('section', 'quiz', 'prev', 'next', 'currentIndex', 'total', 'learningSchema'));
    }

    public function userSubmit(Request $request, Section $section)
    {
        abort_if(! $section->is_active, 404);

        $quizzes = $section->quizzes()->active()->orderBy('quiz_order')->get();
        $answers = $request->input('answers', []);

        $correctCount = 0;
        $results = [];
        $detailedAnswers = [];

        foreach ($quizzes as $index => $quiz) {
            $userAnswerRaw = $answers[$quiz->id] ?? null;
            $userAnswer = $userAnswerRaw !== null ? strtolower(trim((string) $userAnswerRaw)) : null;
            $correctAnswer = strtolower(trim((string) $quiz->correct_answer));
            $isCorrect = $userAnswer !== null && $userAnswer === $correctAnswer;

            if ($isCorrect) {
                $correctCount++;
            }

            $results[$quiz->id] = [
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
            ];

            $userOptionText = $userAnswer ? ($quiz->{'option_'.$userAnswer} ?? null) : null;
            $correctOptionText = $quiz->{'option_'.$correctAnswer} ?? null;

            $detailedAnswers[] = [
                'quiz_id' => $quiz->id,
                'quiz_number' => $index + 1,
                'question' => $quiz->question,
                'user_answer' => $userAnswer ? strtoupper($userAnswer) : '-',
                'user_answer_text' => $userOptionText,
                'correct_answer' => strtoupper($correctAnswer),
                'correct_answer_text' => $correctOptionText,
                'is_correct' => $isCorrect,
            ];
        }

        $totalQuestions = $quizzes->count();
        $scorePercent = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;
        $passingScore = (int) ($section->passing_score ?? 70);
        $passed = $totalQuestions > 0 && $scorePercent >= $passingScore;

        QuizAttempt::create([
            'user_id' => auth()->id(),
            'section_id' => $section->id,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctCount,
            'passing_score' => $passingScore,
            'is_passed' => $passed,
            'answers' => $detailedAnswers,
            'attempted_at' => now(),
        ]);

        if ($passed) {
            UserProgress::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'section_id' => $section->id,
                ],
                [
                    'status' => 'completed',
                    'progress_percentage' => 100,
                    'completed_at' => now(),
                ]
            );
        }

        $learningSchema = $this->resolveLearningSchema($section, $request);

        $nextSection = null;
        if ($learningSchema) {
            $allSections = $learningSchema->sections()
                ->where('sections.is_active', true)
                ->orderBy('learning_schema_section.section_order')
                ->get(['sections.id', 'sections.title']);

            $currentIndex = $allSections->search(fn ($s) => $s->id === $section->id);
            if ($currentIndex !== false && $currentIndex < $allSections->count() - 1) {
                $nextSection = $allSections[$currentIndex + 1];
            }
        }

        return view('user.quizzes.result', array_merge(
            compact('section', 'quizzes', 'results', 'correctCount', 'passed', 'learningSchema', 'nextSection'),
            MateriPreviewContext::variables($learningSchema, false),
        ));
    }

    private function resolveLearningSchema(Section $section, ?Request $request = null): ?LearningSchema
    {
        $schemaId = $request?->query('schema') ?? $request?->input('schema_id');
        $user = auth()->user();

        // 1. Jika ada schema_id spesifik di request, periksa apakah section terkait
        if ($schemaId) {
            $schema = $section->learningSchemas()
                ->where('learning_schemas.id', $schemaId)
                ->first();

            if ($schema) {
                if (! $user || $user->isEnrolledIn($schema->id)) {
                    return $schema;
                }
            }
        }

        // 2. Jika user terautentikasi, prioritaskan schema yang di-enroll oleh user
        if ($user) {
            $enrolledSchema = $section->learningSchemas()
                ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
                ->first();

            if ($enrolledSchema) {
                return $enrolledSchema;
            }
        }

        // 3. Fallback ke schema aktif pertama, atau schema pertama yang terhubung
        return $section->learningSchemas()->where('is_active', true)->first()
            ?? $section->learningSchemas()->first();
    }

    // ── PRIVATE ────────────────────────────────────────────────────────────────────────────

    private function syncMedia(Request $request, Quiz $quiz): void
    {
        $mediaInputs = $request->input('media', []);
        $files = $request->file('media', []);

        foreach ($mediaInputs as $i => $item) {
            $mediaType = $item['media_type'] ?? null;
            if (! $mediaType) {
                continue;
            }

            $filePath = null;
            if (isset($files[$i]['file'])) {
                $filePath = $files[$i]['file']->store('quizzes/media', 'public');
            }

            $quiz->media()->create([
                'media_type' => $mediaType,
                'title' => $item['title'] ?? null,
                'description' => $item['description'] ?? null,
                'url' => $item['url'] ?? null,
                'file_path' => $filePath,
                'media_order' => $item['media_order'] ?? ($i + 1),
                'is_active' => true,
            ]);
        }
    }

    private function authorizeQuiz(Section $section, Quiz $quiz): void
    {
        abort_if($quiz->section_id !== $section->id, 404);
    }

    private function ensureAnswerOptionFilled(array $data): void
    {
        $answer = $data['correct_answer'] ?? null;
        $optionKey = 'option_'.$answer;

        if (in_array($answer, ['c', 'd']) && empty($data[$optionKey])) {
            abort(422, "Jawaban benar '{$answer}' membutuhkan opsi '{$optionKey}' diisi.");
        }
    }
}
