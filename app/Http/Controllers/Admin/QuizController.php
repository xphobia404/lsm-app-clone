<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Quiz;
use App\Services\MediaService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    public function index(Section $section)
    {
        $quizzes = $section->quizzes()->with('media')->get();
        return view('admin.quizzes.index', compact('section', 'quizzes'));
    }

    public function create(Section $section)
    {
        return view('admin.quizzes.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        $filledOptions = $this->getFilledOptions($request);

        $request->validate([
            'question'       => 'required|string',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'nullable|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => ['required', 'in:' . implode(',', $filledOptions)],
        ], [
            'question.required'       => 'Pertanyaan wajib diisi.',
            'option_a.required'       => 'Pilihan A wajib diisi.',
            'correct_answer.required' => 'Jawaban benar wajib dipilih.',
            'correct_answer.in'       => 'Jawaban benar harus dipilih dari opsi yang sudah diisi.',
        ]);

        $quiz = Quiz::create(array_merge(
            $request->only(['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'order', 'explanation']),
            ['section_id' => $section->id]
        ));

        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil ditambahkan. Tambahkan media di halaman Edit.');
    }

    public function edit(Section $section, Quiz $quiz)
    {
        $quiz->load('media');
        return view('admin.quizzes.edit', compact('section', 'quiz'));
    }

    public function update(Request $request, Section $section, Quiz $quiz)
    {
        $filledOptions = $this->getFilledOptions($request);

        $request->validate([
            'question'       => 'required|string',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'nullable|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => ['required', 'in:' . implode(',', $filledOptions)],
        ], [
            'question.required'       => 'Pertanyaan wajib diisi.',
            'option_a.required'       => 'Pilihan A wajib diisi.',
            'correct_answer.required' => 'Jawaban benar wajib dipilih.',
            'correct_answer.in'       => 'Jawaban benar harus dipilih dari opsi yang sudah diisi.',
        ]);

        $quiz->update(
            $request->only(['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'order', 'explanation'])
        );

        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Section $section, Quiz $quiz)
    {
        // Hapus semua media via service
        $this->mediaService->deleteAllMedia($quiz);
        $quiz->delete();

        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil dihapus.');
    }

    private function getFilledOptions(Request $request): array
    {
        $options = ['a'];
        foreach (['b', 'c', 'd'] as $key) {
            if ($request->filled('option_' . $key)) {
                $options[] = $key;
            }
        }
        return $options;
    }
}
