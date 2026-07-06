<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SectionController extends Controller
{
    public function index(Request $request, LearningSchema $learningSchema): JsonResponse
    {
        $sections = $learningSchema->sections()
            ->when($request->filled('is_active'), fn ($q) =>
                $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN))
            )
            ->withCount(['contents', 'quizzes'])
            ->ordered()
            ->paginate($request->get('per_page', 15));

        return response()->json($sections);
    }

    public function store(Request $request, LearningSchema $learningSchema): JsonResponse
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'section_order' => 'sometimes|integer|min:0',
            'is_active'     => 'sometimes|boolean',
        ]);

        if (!isset($validated['section_order'])) {
            $validated['section_order'] = $learningSchema->sections()->max('section_order') + 1;
        }

        $section = $learningSchema->sections()->create($validated);

        return response()->json([
            'message' => 'Section created successfully.',
            'data'    => $section,
        ], 201);
    }

    public function show(LearningSchema $learningSchema, Section $section): JsonResponse
    {
        $this->authorizeSection($learningSchema, $section);

        $section->load(['contents' => fn ($q) => $q->active()->ordered(),
                        'quizzes'  => fn ($q) => $q->active()->ordered(),
                        'media'    => fn ($q) => $q->ordered()]);

        return response()->json($section);
    }

    public function update(Request $request, LearningSchema $learningSchema, Section $section): JsonResponse
    {
        $this->authorizeSection($learningSchema, $section);

        $validated = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'description'   => 'nullable|string',
            'section_order' => 'sometimes|integer|min:0',
            'is_active'     => 'sometimes|boolean',
        ]);

        $section->update($validated);

        return response()->json([
            'message' => 'Section updated successfully.',
            'data'    => $section->fresh(),
        ]);
    }

    public function destroy(LearningSchema $learningSchema, Section $section): JsonResponse
    {
        $this->authorizeSection($learningSchema, $section);

        $section->delete();

        return response()->json(['message' => 'Section deleted successfully.']);
    }

    private function authorizeSection(LearningSchema $learningSchema, Section $section): void
    {
        abort_if($section->learning_schema_id !== $learningSchema->id, 404);
    }
}
