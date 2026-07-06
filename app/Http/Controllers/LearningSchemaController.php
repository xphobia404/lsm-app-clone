<?php

namespace App\Http\Controllers;

use App\Models\LearningSchema;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LearningSchemaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = LearningSchema::query();

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $schemas = $query->withCount('sections')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json($schemas);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'description'=> 'nullable|string',
            'is_active'  => 'sometimes|boolean',
        ]);

        $schema = LearningSchema::create($validated);

        return response()->json([
            'message' => 'Learning schema created successfully.',
            'data'    => $schema,
        ], 201);
    }

    public function show(LearningSchema $learningSchema): JsonResponse
    {
        $learningSchema->load(['sections' => function ($q) {
            $q->active()->ordered()->with(['contents', 'quizzes']);
        }]);

        return response()->json($learningSchema);
    }

    public function update(Request $request, LearningSchema $learningSchema): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'description'=> 'nullable|string',
            'is_active'  => 'sometimes|boolean',
        ]);

        $learningSchema->update($validated);

        return response()->json([
            'message' => 'Learning schema updated successfully.',
            'data'    => $learningSchema->fresh(),
        ]);
    }

    public function destroy(LearningSchema $learningSchema): JsonResponse
    {
        $learningSchema->delete();

        return response()->json(['message' => 'Learning schema deleted successfully.']);
    }
}
