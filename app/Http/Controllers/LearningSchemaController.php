<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\Section;
use Illuminate\Http\Request;

class LearningSchemaController extends Controller
{
    // ── ADMIN ────────────────────────────────────────────────────────────

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

    /**
     * Admin: detail materi + daftar section yang terlampir.
     */
    public function show(LearningSchema $learningSchema)
    {
        $learningSchema->load([
            'sections' => fn ($q) => $q->withCount(['contents', 'quizzes'])
                ->orderByPivot('section_order'),
        ]);

        // Section yang belum terhubung ke materi ini (untuk form attach)
        $availableSections = Section::whereDoesntHave('learningSchemas', fn ($q) =>
            $q->where('learning_schemas.id', $learningSchema->id)
        )->orderBy('title')->get(['id', 'title', 'is_active']);

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

    /**
     * Attach section ke materi dari halaman show admin.
     */
    public function attachSection(Request $request, LearningSchema $learningSchema)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
        ]);

        // Cegah duplikat
        if ($learningSchema->sections()->where('sections.id', $request->section_id)->exists()) {
            return back()->with('error', 'Section sudah terhubung ke materi ini.');
        }

        $maxOrder = $learningSchema->sections()->max('learning_schema_section.section_order') ?? 0;
        $learningSchema->sections()->attach($request->section_id, [
            'section_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Section berhasil dihubungkan.');
    }

    /**
     * Detach section dari materi.
     */
    public function detachSection(LearningSchema $learningSchema, Section $section)
    {
        $learningSchema->sections()->detach($section->id);

        return back()->with('success', 'Section berhasil dilepas dari materi ini.');
    }

    // ── USER-FACING ─────────────────────────────────────────────────

    /**
     * User: list semua materi aktif.
     * Route: GET /app/schemas
     */
    public function userIndex(Request $request)
    {
        $schemas = LearningSchema::active()
            ->withCount(['sections' => fn ($q) => $q->where('is_active', true)])
            ->when($request->filled('search'), fn ($q) =>
                $q->where('title', 'like', "%{$request->search}%")
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Progress user untuk setiap schema
        $user = auth()->user();
        $progressMap = $user->progresses()
            ->select('section_id', 'status')
            ->get()
            ->groupBy('section_id');

        return view('user.schemas.index', compact('schemas', 'progressMap'));
    }

    /**
     * User: detail materi + list section.
     * Route: GET /app/schemas/{learningSchema}
     */
    public function userShow(LearningSchema $learningSchema)
    {
        abort_if(! $learningSchema->is_active, 404);

        $learningSchema->load([
            'sections' => fn ($q) => $q->where('is_active', true)
                ->withCount(['contents', 'quizzes'])
                ->orderByPivot('section_order'),
        ]);

        $user    = auth()->user();
        $progressMap = $user->progresses()
            ->whereIn('section_id', $learningSchema->sections->pluck('id'))
            ->pluck('status', 'section_id');

        return view('user.schemas.show', compact('learningSchema', 'progressMap'));
    }
}
