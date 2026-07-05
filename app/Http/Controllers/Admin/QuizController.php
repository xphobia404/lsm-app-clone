<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $filledOptions = $this->getFilledOptions($request);

        $request->validate([
            'question'       => 'required|string',
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'nullable|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => ['required', 'in:' . implode(',', $filledOptions)],
        ], [
            'question.required'       => 'Pertanyaan wajib diisi.',
            'question_image.image'    => 'File harus berupa gambar.',
            'question_image.max'      => 'Ukuran gambar maksimal 2MB.',
            'option_a.required'       => 'Pilihan A wajib diisi.',
            'correct_answer.required' => 'Jawaban benar wajib dipilih.',
            'correct_answer.in'       => 'Jawaban benar harus dipilih dari opsi yang sudah diisi.',
        ]);

        $data = $request->only(['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'order', 'explanation']);
        $data['section_id'] = $section->id;

        if ($request->hasFile('question_image')) {
            $data['question_image'] = $request->file('question_image')
                ->store('quizzes/images', 'public');
        }

        Quiz::create($data);

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
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'option_a'       => 'required|string|max:255',
            'option_b'       => 'nullable|string|max:255',
            'option_c'       => 'nullable|string|max:255',
            'option_d'       => 'nullable|string|max:255',
            'correct_answer' => ['required', 'in:' . implode(',', $filledOptions)],
        ], [
            'question.required'       => 'Pertanyaan wajib diisi.',
            'question_image.image'    => 'File harus berupa gambar.',
            'question_image.max'      => 'Ukuran gambar maksimal 2MB.',
            'option_a.required'       => 'Pilihan A wajib diisi.',
            'correct_answer.required' => 'Jawaban benar wajib dipilih.',
            'correct_answer.in'       => 'Jawaban benar harus dipilih dari opsi yang sudah diisi.',
        ]);

        $data = $request->only(['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'order', 'explanation']);

        if ($request->hasFile('question_image')) {
            if ($quiz->question_image) {
                Storage::disk('public')->delete($quiz->question_image);
            }
            $data['question_image'] = $request->file('question_image')
                ->store('quizzes/images', 'public');
        }

        if ($request->boolean('remove_image') && $quiz->question_image) {
            Storage::disk('public')->delete($quiz->question_image);
            $data['question_image'] = null;
        }

        $quiz->update($data);

        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Section $section, Quiz $quiz)
    {
        if ($quiz->question_image) {
            Storage::disk('public')->delete($quiz->question_image);
        }

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
