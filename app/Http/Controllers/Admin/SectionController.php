<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSectionRequest;
use App\Http\Requests\Admin\UpdateSectionRequest;
use App\Models\CourseType;
use App\Models\Section;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    // =========================================================================
    // Index
    // =========================================================================

    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        $baseQuery = Section::with('courseType')
            ->withCount('quizzes')
            ->orderBy('order');

        if ($q = $request->input('q')) {
            $baseQuery->where('title', 'like', '%' . $q . '%');
        }

        if ($request->input('status') === 'published') {
            $baseQuery->where('is_published', true);
        } elseif ($request->input('status') === 'draft') {
            $baseQuery->where('is_published', false);
        }

        $courseTypes = CourseType::active()->orderBy('order')->get();

        if ($ct = $request->input('course_type_id')) {
            $sections = (clone $baseQuery)
                ->where('course_type_id', $ct)
                ->paginate($perPage)
                ->withQueryString();

            return view('admin.sections.index', [
                'courseTypes'        => $courseTypes,
                'sections'           => $sections,
                'grouped'            => null,
                'selectedCourseType' => $courseTypes->firstWhere('id', (int) $ct),
            ]);
        }

        $grouped = $baseQuery->get()->groupBy(
            fn (Section $s) => $s->courseType?->name ?? 'Tanpa Spesialisasi'
        );

        return view('admin.sections.index', [
            'courseTypes'        => $courseTypes,
            'sections'           => null,
            'grouped'            => $grouped,
            'selectedCourseType' => null,
        ]);
    }

    // =========================================================================
    // Create / Store
    // =========================================================================

    public function create()
    {
        $courseTypes = CourseType::active()->get();
        return view('admin.sections.create', compact('courseTypes'));
    }

    public function store(StoreSectionRequest $request)
    {
        $data = $request->validated();

        $data['order']      = $data['order'] ?? (Section::max('order') + 1);
        $data['slug']       = Str::slug($data['title']) . '-' . time();
        $data['created_by'] = Auth::id();
        $data['content']    = null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->mediaService->storeDirect(
                $request->file('thumbnail'),
                'sections/thumbnails'
            );
        }

        $data['pages'] = ! empty($data['pages'])
            ? $this->handleSlideMedia($request, $data['pages'])
            : [];

        Section::create($data);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section berhasil dibuat.');
    }

    // =========================================================================
    // Edit / Update
    // =========================================================================

    public function edit(Section $section)
    {
        $courseTypes = CourseType::active()->get();
        return view('admin.sections.edit', compact('section', 'courseTypes'));
    }

    public function update(UpdateSectionRequest $request, Section $section)
    {
        $data = $request->validated();

        $data['content'] = null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->mediaService->replaceDirect(
                $request->file('thumbnail'),
                'sections/thumbnails',
                $section->thumbnail
            );
        }

        $data['pages'] = ! empty($data['pages'])
            ? $this->handleSlideMedia($request, $data['pages'], $section->pages ?? [])
            : [];

        $section->update($data);

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section berhasil diperbarui.');
    }

    // =========================================================================
    // Destroy
    // =========================================================================

    public function destroy(Section $section)
    {
        $this->mediaService->deletePath($section->thumbnail);
        $this->deleteAllSlideMedia($section->pages ?? []);
        $this->mediaService->deleteAllMedia($section);
        $section->delete();

        return redirect()->route('admin.sections.index')
            ->with('success', 'Section berhasil dihapus.');
    }

    public function togglePublish(Section $section)
    {
        $section->update(['is_published' => ! $section->is_published]);

        $msg = $section->is_published
            ? 'Section berhasil dipublikasikan.'
            : 'Section berhasil disembunyikan.';

        return back()->with('success', $msg);
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function handleSlideMedia(\Illuminate\Http\Request $request, array $pages, array $oldPages = []): array
    {
        $uploaded = $request->file('pages') ?? [];

        foreach ($pages as $idx => &$page) {
            $mediaType = $page['slide_media_type'] ?? 'none';

            if ($mediaType === 'image' && isset($uploaded[$idx]['new_image'])
                && $uploaded[$idx]['new_image']->isValid()) {
                $this->mediaService->deletePath($oldPages[$idx]['image_path'] ?? null);
                $path = $this->mediaService->storeDirect($uploaded[$idx]['new_image'], 'sections/slides/images');
                $page['image_url']  = $this->mediaService->url($path);
                $page['image_path'] = $path;
            }
            unset($page['new_image']);

            if ($mediaType === 'video_upload' && isset($uploaded[$idx]['new_video'])
                && $uploaded[$idx]['new_video']->isValid()) {
                $this->mediaService->deletePath($oldPages[$idx]['video_path'] ?? null);
                $path = $this->mediaService->storeDirect($uploaded[$idx]['new_video'], 'sections/slides/videos');
                $page['video_url']  = $this->mediaService->url($path);
                $page['video_path'] = $path;
            }
            unset($page['new_video']);

            if ($mediaType === 'audio' && isset($uploaded[$idx]['new_audio'])
                && $uploaded[$idx]['new_audio']->isValid()) {
                $this->mediaService->deletePath($oldPages[$idx]['audio_path'] ?? null);
                $path = $this->mediaService->storeDirect($uploaded[$idx]['new_audio'], 'sections/slides/audios');
                $page['audio_url']  = $this->mediaService->url($path);
                $page['audio_path'] = $path;
            }
            unset($page['new_audio']);
        }
        unset($page);

        return $pages;
    }

    private function deleteAllSlideMedia(array $pages): void
    {
        foreach ($pages as $page) {
            $this->mediaService->deletePaths([
                $page['image_path'] ?? null,
                $page['video_path'] ?? null,
                $page['audio_path'] ?? null,
            ]);
        }
    }
}
