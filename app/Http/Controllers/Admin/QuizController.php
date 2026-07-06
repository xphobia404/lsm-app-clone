<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Http\Requests\Admin\UpdateQuizRequest;
use App\Models\Quiz;
use App\Models\Section;
use App\Services\MediaService;

class QuizController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    // =========================================================================
    // Index
    // =========================================================================

    public function index(Section $section)
    {
        $quizzes = $section->quizzes()->with('media')->orderBy('order')->get();

        return view('admin.quizzes.index', compact('section', 'quizzes'));
    }

    // =========================================================================
    // Create / Store
    // =========================================================================

    public function create(Section $section)
    {
        return view('admin.quizzes.create', compact('section'));
    }

    public function store(StoreQuizRequest $request, Section $section)
    {
        Quiz::create(array_merge(
            $request->validated(),
            ['section_id' => $section->id]
        ));

        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil ditambahkan. Tambahkan media di halaman Edit.');
    }

    // =========================================================================
    // Edit / Update
    // =========================================================================

    public function edit(Section $section, Quiz $quiz)
    {
        $quiz->load('media');
        return view('admin.quizzes.edit', compact('section', 'quiz'));
    }

    public function update(UpdateQuizRequest $request, Section $section, Quiz $quiz)
    {
        $quiz->update($request->validated());

        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    // =========================================================================
    // Destroy
    // =========================================================================

    public function destroy(Section $section, Quiz $quiz)
    {
        $this->mediaService->deleteAllMedia($quiz);
        $quiz->delete();

        return redirect()->route('admin.sections.quizzes.index', $section)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
