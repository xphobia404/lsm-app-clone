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

        $grouped = $baseQuery->get()->groupBy(fn ($s) => $s->courseType?->name ?? 'Tanpa Spesialisasi');

        return view('admin.sections.index', [
            'courseTypes'        => $courseTypes,
            'sections'           => null,
            'grouped'            => $grouped,
            'selectedCourseType' => null,
        ]);
    }

    // =========================================================================
    // Create
    // =========================================================================

    public function create()
    {
        $courseTypes = CourseType::active()->get();
        return view('admin.sections.create', compact('courseTypes'));
    }

    public function store(Request $request)
    {
        $data = $this->validateSection($request);

        $data['order'] = $data['order'] ?? (Section::max('order') + 1);
        $data['slug']  = Str::slug($data['title']) . '-' . time();

        // Thumbnail
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->mediaService->storeDirect(
                $request->file('thumbnail'),
                'sections/thumbnails'
            );
        }

        // Video / Audio utama
        $this->handleSectionMainMedia($request, $data);

        // Multi-page images & audio
        if (($data['content_mode'] ?? 'single') === 'multi' && !empty($data['pages'])) {
            $data['pages'] = $this->handlePageMedia($request, $data['pages']);
        }

        // Bersihkan field tidak relevan
        if (($data['content_mode'] ?? 'single') === 'multi') {
            $data['content'] = null;
        } else {
            $data['pages'] = null;
        }

        Section::create($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil dibuat.');
    }

    // =========================================================================
    // Edit
    // =========================================================================

    public function edit(Section $section)
    {
        $courseTypes = CourseType::active()->get();
        return view('admin.sections.edit', compact('section', 'courseTypes'));
    }

    public function update(Request $request, Section $section)
    {
        $data = $this->validateSection($request);

        // Thumbnail
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->mediaService->replaceDirect(
                $request->file('thumbnail'),
                'sections/thumbnails',
                $section->thumbnail
            );
        }

        // Video / Audio utama — hapus lama jika ada file baru
        if ($request->hasFile('video_file') || $request->hasFile('audio_file')) {
            if ($section->media_file && in_array($section->media_type, ['video_upload', 'audio_upload'])) {
                $this->mediaService->deletePath($section->media_file);
            }
        }
        $this->handleSectionMainMedia($request, $data);

        // Multi-page images & audio
        if (($data['content_mode'] ?? 'single') === 'multi' && !empty($data['pages'])) {
            $data['pages'] = $this->handlePageMedia($request, $data['pages'], $section->pages ?? []);
        }

        // Jika mode berubah dari multi → single, hapus semua media per-slide
        if (($data['content_mode'] ?? 'single') === 'single' && $section->content_mode === 'multi') {
            $this->deleteAllPageMedia($section->pages ?? []);
        }

        // Bersihkan field tidak relevan
        if (($data['content_mode'] ?? 'single') === 'multi') {
            $data['content'] = null;
        } else {
            $data['pages'] = null;
        }

        $section->update($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil diperbarui.');
    }

    // =========================================================================
    // Destroy
    // =========================================================================

    public function destroy(Section $section)
    {
        // Hapus file storage langsung
        $this->mediaService->deletePaths([
            $section->thumbnail,
            in_array($section->media_type, ['video_upload', 'audio_upload']) ? $section->media_file : null,
        ]);

        // Hapus gambar & audio tiap page
        $this->deleteAllPageMedia($section->pages ?? []);

        // Hapus semua polymorphic media (tabel media)
        $this->mediaService->deleteAllMedia($section);

        $section->delete();

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil dihapus.');
    }

    // =========================================================================
    // Toggle Publish
    // =========================================================================

    public function togglePublish(Section $section)
    {
        $section->update(['is_published' => !$section->is_published]);
        $msg = $section->is_published ? 'Section berhasil dipublikasikan.' : 'Section berhasil disembunyikan.';
        return back()->with('success', $msg);
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /**
     * Validasi request section (dipakai store & update).
     */
    private function validateSection(Request $request): array
    {
        return $request->validate([
            'course_type_id'      => 'nullable|exists:course_types,id',
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'content_mode'        => 'required|in:single,multi',
            'content'             => 'nullable|string',
            'pages'               => 'nullable|array',
            'pages.*.title'       => 'nullable|string|max:255',
            'pages.*.content'     => 'nullable|string',
            'pages.*.image_url'   => 'nullable|string',
            'pages.*.audio_url'   => 'nullable|string',
            'pages.*.new_audio'   => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac,audio/x-wav|max:51200',
            'media_type'          => 'required|in:video_upload,audio_upload,youtube,drive',
            'media_url'           => 'nullable|url',
            'video_file'          => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime|max:204800',
            'audio_file'          => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac,audio/x-wav|max:51200',
            'thumbnail'           => 'nullable|image|max:5120',
            'order'               => 'nullable|integer|min:0',
            'is_published'        => 'boolean',
        ]);
    }

    /**
     * Proses upload video / audio utama section, atau simpan URL eksternal.
     * Menggunakan MediaService::storeDirect() — hasil disimpan ke kolom media_file.
     */
    private function handleSectionMainMedia(Request $request, array &$data): void
    {
        $type = $data['media_type'] ?? $request->input('media_type');

        if ($type === 'video_upload' && $request->hasFile('video_file')) {
            $data['media_file'] = $this->mediaService->storeDirect(
                $request->file('video_file'),
                'sections/videos'
            );
            $data['media_url'] = null;

        } elseif ($type === 'audio_upload' && $request->hasFile('audio_file')) {
            $data['media_file'] = $this->mediaService->storeDirect(
                $request->file('audio_file'),
                'sections/audios'
            );
            $data['media_url'] = null;

        } elseif (in_array($type, ['youtube', 'drive'])) {
            $data['media_url']  = $request->input('media_url');
            $data['media_file'] = null;
        }
    }

    /**
     * Upload gambar & audio per-slide di multi-page mode.
     * Menggunakan MediaService::storeDirect() & deletePath().
     */
    private function handlePageMedia(Request $request, array $pages, array $oldPages = []): array
    {
        $uploadedFiles = $request->file('pages') ?? [];

        foreach ($pages as $idx => &$page) {

            // Gambar per page
            if (isset($uploadedFiles[$idx]['new_image'])
                && $uploadedFiles[$idx]['new_image']->isValid()) {

                $path = $this->mediaService->storeDirect(
                    $uploadedFiles[$idx]['new_image'],
                    'sections/pages'
                );
                $page['image_url']  = $this->mediaService->url($path);
                $page['image_path'] = $path;
            }
            unset($page['new_image']);

            // Audio per page
            if (isset($uploadedFiles[$idx]['new_audio'])
                && $uploadedFiles[$idx]['new_audio']->isValid()) {

                $oldAudioPath = $oldPages[$idx]['audio_path'] ?? null;
                $this->mediaService->deletePath($oldAudioPath);

                $audioPath = $this->mediaService->storeDirect(
                    $uploadedFiles[$idx]['new_audio'],
                    'sections/pages/audios'
                );
                $page['audio_url']  = $this->mediaService->url($audioPath);
                $page['audio_path'] = $audioPath;
            }

            // Hapus audio slide jika admin centang hapus
            if (!empty($page['remove_audio'])) {
                $this->mediaService->deletePath($oldPages[$idx]['audio_path'] ?? null);
                $page['audio_url']  = null;
                $page['audio_path'] = null;
            }

            unset($page['remove_audio'], $page['new_audio']);
        }
        unset($page);

        return $pages;
    }

    /**
     * Hapus semua file gambar & audio pada tiap page.
     */
    private function deleteAllPageMedia(array $pages): void
    {
        foreach ($pages as $page) {
            $this->mediaService->deletePaths([
                $page['image_path'] ?? null,
                $page['audio_path'] ?? null,
            ]);
        }
    }
}
