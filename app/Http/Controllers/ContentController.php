<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    public function index(Request $request, Section $section): JsonResponse
    {
        $contents = $section->contents()
            ->when($request->filled('is_active'), fn ($q) =>
                $q->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN))
            )
            ->ordered()
            ->paginate($request->get('per_page', 15));

        return response()->json($contents);
    }

    public function store(Request $request, Section $section): JsonResponse
    {
        $validated = $request->validate([
            'description'   => 'required|string',
            'content_order' => 'sometimes|integer|min:0',
            'is_active'     => 'sometimes|boolean',
        ]);

        if (!isset($validated['content_order'])) {
            $validated['content_order'] = $section->contents()->max('content_order') + 1;
        }

        $content = $section->contents()->create($validated);

        return response()->json([
            'message' => 'Content created successfully.',
            'data'    => $content,
        ], 201);
    }

    public function show(Section $section, Content $content): JsonResponse
    {
        $this->authorizeContent($section, $content);

        $content->load(['media' => fn ($q) => $q->ordered()]);

        return response()->json($content);
    }

    public function update(Request $request, Section $section, Content $content): JsonResponse
    {
        $this->authorizeContent($section, $content);

        $validated = $request->validate([
            'description'   => 'sometimes|string',
            'content_order' => 'sometimes|integer|min:0',
            'is_active'     => 'sometimes|boolean',
        ]);

        $content->update($validated);

        return response()->json([
            'message' => 'Content updated successfully.',
            'data'    => $content->fresh(),
        ]);
    }

    public function destroy(Section $section, Content $content): JsonResponse
    {
        $this->authorizeContent($section, $content);

        $content->delete();

        return response()->json(['message' => 'Content deleted successfully.']);
    }

    private function authorizeContent(Section $section, Content $content): void
    {
        abort_if($content->section_id !== $section->id, 404);
    }
}
