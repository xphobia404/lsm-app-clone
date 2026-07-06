<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function userShow(LearningSchema $learningSchema, Section $section)
    {
        $section->load([
            'contents' => fn ($q) => $q->active()->ordered(),
            'quizzes'  => fn ($q) => $q->where('is_active', true),
        ]);

        // Urutan section dalam schema ini (untuk next/prev section)
        $allSections = $learningSchema->sections()
            ->where('is_active', true)
            ->orderBy('learning_schema_section.section_order')
            ->get(['sections.id', 'sections.title']);

        $currentIndex = $allSections->search(fn ($s) => $s->id === $section->id);
        $prevSection  = $currentIndex > 0 ? $allSections[$currentIndex - 1] : null;
        $nextSection  = $currentIndex < $allSections->count() - 1 ? $allSections[$currentIndex + 1] : null;

        return view('user.section', compact(
            'learningSchema', 'section', 'prevSection', 'nextSection'
        ));
    }

    // ── Admin methods ──────────────────────────────────────────────────────────

    public function allIndex(Request $request)
    {
        $query = Section::withCount(['contents', 'quizzes'])
            ->with('learningSchemas:id,title');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $sections = $query->orderBy('title')->paginate(15)->withQueryString();

        return view('admin.sections.index', compact('sections'));
    }

    public function schemaIndex(Request $request, LearningSchema $learningSchema)
    {
        $query = $learningSchema->sections()
            ->withCount(['contents', 'quizzes'])
            ->orderBy('learning_schema_section.section_order');

        if ($request->filled('search')) {
            $query->where('sections.title', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('sections.is_active', $request->status === 'active');
        }

        $sections = $query->paginate(15)->withQueryString();

        return view('admin.sections.index', [
            'sections'       => $sections,
            'learningSchema' => $learningSchema,
        ]);
    }

    public function create()
    {
        $learningSchemas = LearningSchema::orderBy('title')->get(['id', 'title']);
        return view('admin.sections.create', compact('learningSchemas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string|max:2000',
            'is_active'            => 'sometimes|boolean',
            'learning_schema_ids'  => 'nullable|array',
            'learning_schema_ids.*'=> 'exists:learning_schemas,id',
        ]);

        $section = Section::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        if (! empty($validated['learning_schema_ids'])) {
            $pivotData = [];
            foreach ($validated['learning_schema_ids'] as $schemaId) {
                $maxOrder = LearningSchema::find($schemaId)
                    ->sections()->max('learning_schema_section.section_order') ?? 0;
                $pivotData[$schemaId] = ['section_order' => $maxOrder + 1];
            }
            $section->learningSchemas()->attach($pivotData);
        }

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section berhasil dibuat.');
    }

    public function edit(Section $section)
    {
        $learningSchemas   = LearningSchema::orderBy('title')->get(['id', 'title']);
        $attachedSchemaIds = $section->learningSchemas->pluck('id')->toArray();
        return view('admin.sections.edit', compact('section', 'learningSchemas', 'attachedSchemaIds'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string|max:2000',
            'is_active'            => 'sometimes|boolean',
            'learning_schema_ids'  => 'nullable|array',
            'learning_schema_ids.*'=> 'exists:learning_schemas,id',
        ]);

        $section->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        $schemaIds = $validated['learning_schema_ids'] ?? [];
        $syncData  = [];
        foreach ($schemaIds as $schemaId) {
            $existing = $section->learningSchemas()->where('learning_schemas.id', $schemaId)->first();
            $order    = $existing
                ? $existing->pivot->section_order
                : (LearningSchema::find($schemaId)->sections()->max('learning_schema_section.section_order') + 1);
            $syncData[$schemaId] = ['section_order' => $order];
        }
        $section->learningSchemas()->sync($syncData);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section berhasil diperbarui.');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('admin.sections.index')
            ->with('success', 'Section berhasil dihapus.');
    }

    public function toggleActive(Section $section)
    {
        $section->update(['is_active' => ! $section->is_active]);
        $status = $section->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Section berhasil {$status}.");
    }
}
