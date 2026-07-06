<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Models\Section;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    public function __construct(private readonly MediaService $mediaService) {}

    // =========================================================================
    // List
    // =========================================================================

    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
            ? (int) $request->input('per_page') : 10;

        $baseQuery = Section::with('courseType')->withCount('quizzes')->orderBy('order');

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
                ->paginate($perPage)->withQueryString();

            return view('admin.sections.index', [
                'courseTypes'        => $courseTypes,
                'sections'           => $sections,
                'grouped'            => null,
                'selectedCourseType' => $courseTypes->firstWhere('id', (int) $ct),
            ]);
        }

        $grouped = $baseQuery->get()->groupBy(fn ($s) => $s->courseType?->name ?? 'Tanpa Spesialisasi');

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

    public function store(Request $request)
    {
        $data = $this->validateSection($request);

        $data['order']        = $data['order'] ?? (Section::max('order') + 1);
        $data['slug']         = Str::slug($data['title']) . '-' . time();
        $data['content_mode'] = 'multi';
        $data['content']      = null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->mediaService->storeDirect(
                $request->file('thumbnail'), 'sections/thumbnails'
            );
        }

        if (!empty($data['pages'])) {
            $data['pages'] = $this->handleSlideMedia($request, $data['pages']);
        } else {
            $data['pages'] = [];
        }

        Section::create($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil dibuat.');
    }

    // =========================================================================
    // Edit / Update
    // =========================================================================

    public function edit(Section $section)
    {
        $courseTypes = CourseType::active()->get();
        return view('admin.sections.edit', compact('section', 'courseTypes'));
    }

    public function update(Request $request, Section $section)
    {
        $data = $this->validateSection($request);

        $data['content_mode'] = 'multi';
        $data['content']      = null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->mediaService->replaceDirect(
                $request->file('thumbnail'), 'sections/thumbnails', $section->thumbnail
            );
        }

        if (!empty($data['pages'])) {
            $data['pages'] = $this->handleSlideMedia($request, $data['pages'], $section->pages ?? []);
        } else {
            $data['pages'] = [];
        }

        $section->update($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil diperbarui.');
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

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil dihapus.');
    }

    public function togglePublish(Section $section)
    {
        $section->update(['is_published' => !$section->is_published]);
        $msg = $section->is_published ? 'Section berhasil dipublikasikan.' : 'Section berhasil disembunyikan.';
        return back()->with('success', $msg);
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function validateSection(Request $request): array
    {
        return $request->validate([
            'course_type_id'            => 'nullable|exists:course_types,id',
            'title'                     => 'required|string|max:255',
            'description'               => 'nullable|string',
            'thumbnail'                 => 'nullable|image|max:5120',
            'order'                     => 'nullable|integer|min:0',
            'is_published'              => 'boolean',
            // pages / slides
            'pages'                     => 'nullable|array',
            'pages.*.title'             => 'nullable|string|max:255',
            'pages.*.content'           => 'nullable|string',
            'pages.*.slide_media_type'  => 'nullable|string|in:none,image,video_upload,audio,youtube,drive',
            'pages.*.image_url'         => 'nullable|string',
            'pages.*.image_path'        => 'nullable|string',
            'pages.*.video_url'         => 'nullable|string',
            'pages.*.video_path'        => 'nullable|string',
            'pages.*.audio_url'         => 'nullable|string',
            'pages.*.audio_path'        => 'nullable|string',
            'pages.*.youtube_url'       => 'nullable|url',
            'pages.*.drive_url'         => 'nullable|url',
            'pages.*.new_image'         => 'nullable|file|image|max:5120',
            'pages.*.new_video'         => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo|max:204800',
            'pages.*.new_audio'         => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac|max:51200',
        ]);
    }

    /**
     * Proses upload media per slide.
     * Mendukung: image, video_upload, audio, youtube, drive.
     */
    private function handleSlideMedia(Request $request, array $pages, array $oldPages = []): array
    {
        $uploaded = $request->file('pages') ?? [];

        foreach ($pages as $idx => &$page) {
            $mediaType = $page['slide_media_type'] ?? 'none';

            // ── Gambar ──────────────────────────────────────────────────────
            if ($mediaType === 'image' && isset($uploaded[$idx]['new_image'])
                && $uploaded[$idx]['new_image']->isValid()) {

                $this->mediaService->deletePath($oldPages[$idx]['image_path'] ?? null);
                $path = $this->mediaService->storeDirect($uploaded[$idx]['new_image'], 'sections/slides/images');
                $page['image_url']  = $this->mediaService->url($path);
                $page['image_path'] = $path;
            }
            unset($page['new_image']);

            // ── Video Upload ─────────────────────────────────────────────────
            if ($mediaType === 'video_upload' && isset($uploaded[$idx]['new_video'])
                && $uploaded[$idx]['new_video']->isValid()) {

                $this->mediaService->deletePath($oldPages[$idx]['video_path'] ?? null);
                $path = $this->mediaService->storeDirect($uploaded[$idx]['new_video'], 'sections/slides/videos');
                $page['video_url']  = $this->mediaService->url($path);
                $page['video_path'] = $path;
            }
            unset($page['new_video']);

            // ── Audio ─────────────────────────────────────────────────────────
            if ($mediaType === 'audio' && isset($uploaded[$idx]['new_audio'])
                && $uploaded[$idx]['new_audio']->isValid()) {

                $this->mediaService->deletePath($oldPages[$idx]['audio_path'] ?? null);
                $path = $this->mediaService->storeDirect($uploaded[$idx]['new_audio'], 'sections/slides/audios');
                $page['audio_url']  = $this->mediaService->url($path);
                $page['audio_path'] = $path;
            }
            unset($page['new_audio']);

            // ── YouTube / Drive: hanya simpan URL, tidak ada upload ───────────
            // Sudah di-handle oleh hidden input dari view, tidak perlu proses khusus.
        }
        unset($page);

        return $pages;
    }

    /**
     * Hapus semua file media per slide saat section dihapus.
     */
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
