<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Section $section)
    {
        $quizzes = $section->quizzes;
        return view('admin.quizzes.index', compact('section', 'quizzes'));
    }

    public function create(Section $section)
    {
        return view('admin.quizzes.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        // Tentukan opsi mana yang diisi untuk validasi jawaban benar
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

        Quiz::create(array_merge(
            $request->only(['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'order', 'explanation']),
            ['section_id' => $section->id]
        ));

        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Section $section, Quiz $quiz)
    {
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
        $quiz->delete();
        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Kembalikan array opsi yang diisi (minimal ['a']).
     */
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
