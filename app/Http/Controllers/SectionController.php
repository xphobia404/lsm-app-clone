<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Flat list semua section (bottom nav).
     */
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

    public function create()
    {
        $learningSchemas = LearningSchema::orderBy('title')->get(['id', 'title']);
        return view('admin.sections.create', compact('learningSchemas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string|max:2000',
            'is_active'          => 'sometimes|boolean',
            'learning_schema_ids' => 'nullable|array',
            'learning_schema_ids.*' => 'exists:learning_schemas,id',
        ]);

        $section = Section::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        // Attach ke learning schemas yang dipilih
        if (!empty($validated['learning_schema_ids'])) {
            $pivotData = [];
            foreach ($validated['learning_schema_ids'] as $order => $schemaId) {
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
        $learningSchemas    = LearningSchema::orderBy('title')->get(['id', 'title']);
        $attachedSchemaIds  = $section->learningSchemas->pluck('id')->toArray();
        return view('admin.sections.edit', compact('section', 'learningSchemas', 'attachedSchemaIds'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string|max:2000',
            'is_active'          => 'sometimes|boolean',
            'learning_schema_ids' => 'nullable|array',
            'learning_schema_ids.*' => 'exists:learning_schemas,id',
        ]);

        $section->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        // Sync relasi (tambah/hapus schema yang tidak dipilih)
        $schemaIds = $validated['learning_schema_ids'] ?? [];
        $syncData  = [];
        foreach ($schemaIds as $schemaId) {
            $existing = $section->learningSchemas()->where('learning_schemas.id', $schemaId)->first();
            $order    = $existing ? $existing->pivot->section_order
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
