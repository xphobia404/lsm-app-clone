<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request, LearningSchema $learningSchema)
    {
        $query = $learningSchema->sections()->withCount(['contents', 'quizzes']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sections = $query->ordered()->paginate(10)->withQueryString();

        return view('admin.sections.index', compact('learningSchema', 'sections'));
    }

    public function create(LearningSchema $learningSchema)
    {
        $section = null;
        $nextOrder = $learningSchema->sections()->max('section_order') + 1;
        return view('admin.sections.create', compact('learningSchema', 'section', 'nextOrder'));
    }

    public function store(Request $request, LearningSchema $learningSchema)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:2000',
            'section_order' => 'required|integer|min:1',
            'is_active'     => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $learningSchema->sections()->create($validated);

        return redirect()
            ->route('admin.learning-schemas.sections.index', $learningSchema)
            ->with('success', 'Section berhasil ditambahkan.');
    }

    public function show(LearningSchema $learningSchema, Section $section)
    {
        $this->authorizeSection($learningSchema, $section);
        $section->load(['contents', 'quizzes', 'media']);
        return view('admin.sections.show', compact('learningSchema', 'section'));
    }

    public function edit(LearningSchema $learningSchema, Section $section)
    {
        $this->authorizeSection($learningSchema, $section);
        return view('admin.sections.edit', compact('learningSchema', 'section'));
    }

    public function update(Request $request, LearningSchema $learningSchema, Section $section)
    {
        $this->authorizeSection($learningSchema, $section);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:2000',
            'section_order' => 'required|integer|min:1',
            'is_active'     => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $section->update($validated);

        return redirect()
            ->route('admin.learning-schemas.sections.index', $learningSchema)
            ->with('success', 'Section berhasil diperbarui.');
    }

    public function destroy(LearningSchema $learningSchema, Section $section)
    {
        $this->authorizeSection($learningSchema, $section);
        $section->delete();

        return redirect()
            ->route('admin.learning-schemas.sections.index', $learningSchema)
            ->with('success', 'Section berhasil dihapus.');
    }

    public function toggleActive(LearningSchema $learningSchema, Section $section)
    {
        $this->authorizeSection($learningSchema, $section);
        $section->update(['is_active' => ! $section->is_active]);
        $status = $section->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Section berhasil {$status}.");
    }

    private function authorizeSection(LearningSchema $learningSchema, Section $section): void
    {
        abort_if($section->learning_schema_id !== $learningSchema->id, 404);
    }
}
