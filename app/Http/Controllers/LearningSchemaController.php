<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use Illuminate\Http\Request;

class LearningSchemaController extends Controller
{
    // ───────────────────────────────────────────────────────
    // ADMIN - return Blade views
    // ───────────────────────────────────────────────────────

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
        $learningSchema = null;
        return view('admin.learning-schemas.create', compact('learningSchema'));
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
            ->with('success', 'Learning schema berhasil ditambahkan.');
    }

    public function show(LearningSchema $learningSchema)
    {
        // User-facing show (read only)
        $learningSchema->load(['sections' => fn ($q) => $q->active()->ordered()->with(['contents', 'quizzes'])]);
        return view('user.schemas.show', compact('learningSchema'));
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
            ->with('success', 'Learning schema berhasil diperbarui.');
    }

    public function destroy(LearningSchema $learningSchema)
    {
        $learningSchema->delete();

        return redirect()
            ->route('admin.learning-schemas.index')
            ->with('success', 'Learning schema berhasil dihapus.');
    }

    public function toggleActive(LearningSchema $learningSchema)
    {
        $learningSchema->update(['is_active' => ! $learningSchema->is_active]);

        $status = $learningSchema->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Learning schema berhasil {$status}.");
    }
}
