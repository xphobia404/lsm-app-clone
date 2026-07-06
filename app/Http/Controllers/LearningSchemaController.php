<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\Section;
use Illuminate\Http\Request;

class LearningSchemaController extends Controller
{
    // ── ADMIN ──────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = LearningSchema::withCount('sections');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $learningSchemas = $query->latest()->paginate(10)->withQueryString();

        return view('admin.learning-schemas.index', compact('learningSchemas'));
    }

    public function create()
    {
        return view('admin.learning-schemas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        LearningSchema::create($validated);

        return redirect()
            ->route('admin.learning-schemas.index')
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function show(LearningSchema $learningSchema)
    {
        $learningSchema->load([
            // Fix: hanya tampilkan sections yang aktif di halaman detail admin
            'sections' => fn ($q) => $q->where('sections.is_active', true)
                ->withCount(['contents', 'quizzes'])
                ->orderBy('learning_schema_section.section_order'),
        ]);

        $availableSections = Section::whereDoesntHave('learningSchemas', fn ($q) =>
            $q->where('learning_schemas.id', $learningSchema->id)
        )->where('is_active', true)->orderBy('title')->get(['id', 'title', 'is_active']);

        return view('admin.learning-schemas.show', compact('learningSchema', 'availableSections'));
    }

    public function edit(LearningSchema $learningSchema)
    {
        return view('admin.learning-schemas.edit', compact('learningSchema'));
    }

    public function update(Request $request, LearningSchema $learningSchema)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $learningSchema->update($validated);

        return redirect()
            ->route('admin.learning-schemas.index')
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(LearningSchema $learningSchema)
    {
        $learningSchema->delete();

        return redirect()
            ->route('admin.learning-schemas.index')
            ->with('success', 'Materi berhasil dihapus.');
    }

    public function toggleActive(LearningSchema $learningSchema)
    {
        $learningSchema->update(['is_active' => ! $learningSchema->is_active]);
        $label = $learningSchema->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Materi berhasil {$label}.");
    }

    public function attachSection(Request $request, LearningSchema $learningSchema)
    {
        $request->validate(['section_id' => 'required|exists:sections,id']);

        if ($learningSchema->sections()->where('sections.id', $request->section_id)->exists()) {
            return back()->with('error', 'Section sudah terhubung ke materi ini.');
        }

        $maxOrder = $learningSchema->sections()
            ->max('learning_schema_section.section_order') ?? 0;

        $learningSchema->sections()->attach($request->section_id, [
            'section_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Section berhasil dihubungkan.');
    }

    public function detachSection(LearningSchema $learningSchema, Section $section)
    {
        $learningSchema->sections()->detach($section->id);

        return back()->with('success', 'Section berhasil dilepas dari materi ini.');
    }

    // ── USER-FACING ─────────────────────────────────────────────────────────────

    public function userIndex(Request $request)
    {
        $user = auth()->user();

        // Hanya schema yang di-enroll admin ke user ini
        $schemas = $user->learningSchemas()
            ->with(['sections' => fn ($q) => $q->where('is_active', true)])
            ->when($request->filled('search'), fn ($q) =>
                $q->where('title', 'like', "%{$request->search}%")
            )
            ->paginate(12)
            ->withQueryString();

        // progressMap: section_id => status string
        $allSectionIds = $schemas->flatMap(fn ($s) => $s->sections->pluck('id'));
        $progressMap   = $user->progresses()
            ->whereIn('section_id', $allSectionIds)
            ->pluck('status', 'section_id');

        return view('user.schemas.index', compact('schemas', 'progressMap'));
    }

    public function userShow(LearningSchema $learningSchema)
    {
        // Pastikan user memang di-enroll ke schema ini
        abort_unless(
            auth()->user()->isEnrolledIn($learningSchema->id),
            403,
            'Kamu belum terdaftar di materi ini.'
        );

        $learningSchema->load([
            'sections' => fn ($q) => $q->where('is_active', true)
                ->withCount(['contents', 'quizzes']),
        ]);

        $user        = auth()->user();
        $progressMap = $user->progresses()
            ->whereIn('section_id', $learningSchema->sections->pluck('id'))
            ->pluck('status', 'section_id');

        return view('user.schemas.show', compact('learningSchema', 'progressMap'));
    }
}
