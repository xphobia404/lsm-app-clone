<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50])
            ? (int) $request->input('per_page')
            : 10;

        $baseQuery = Section::with('courseType')
            ->withCount('quizzes')
            ->orderBy('order');

        if ($q = $request->input('q')) {
            $baseQuery->where('title', 'like', '%'.$q.'%');
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

            $selectedCourseType = $courseTypes->firstWhere('id', (int) $ct);

            return view('admin.sections.index', [
                'courseTypes'        => $courseTypes,
                'sections'           => $sections,
                'grouped'            => null,
                'selectedCourseType' => $selectedCourseType,
            ]);
        }

        $allSections = $baseQuery->get();
        $grouped     = $allSections->groupBy(fn ($s) => $s->courseType?->name ?? 'Tanpa Spesialisasi');

        return view('admin.sections.index', [
            'courseTypes'        => $courseTypes,
            'sections'           => null,
            'grouped'            => $grouped,
            'selectedCourseType' => null,
        ]);
    }

    public function create()
    {
        $courseTypes = CourseType::active()->get();
        return view('admin.sections.create', compact('courseTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_type_id'          => 'nullable|exists:course_types,id',
            'title'                   => 'required|string|max:255',
            'description'             => 'nullable|string',
            'content_mode'            => 'required|in:single,multi',
            'content'                 => 'nullable|string',
            'pages'                   => 'nullable|array',
            'pages.*.title'           => 'nullable|string|max:255',
            'pages.*.content'         => 'nullable|string',
            'pages.*.image_url'       => 'nullable|string',
            'pages.*.audio_url'       => 'nullable|string',
            'pages.*.new_audio'       => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac,audio/x-wav|max:51200',
            'media_type'              => 'required|in:video_upload,audio_upload,youtube,drive',
            'media_url'               => 'nullable|url',
            'video_file'              => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime|max:204800',
            'audio_file'              => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac,audio/x-wav|max:51200',
            'thumbnail'               => 'nullable|image|max:5120',
            'order'                   => 'nullable|integer|min:0',
            'is_published'            => 'boolean',
        ]);

        $data['order'] = $data['order'] ?? (Section::max('order') + 1);
        $data['slug']  = Str::slug($data['title']) . '-' . time();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('sections/thumbnails', 'public');
        }

        $this->handleMediaUpload($request, $data);

        // Handle multi-page images & audio upload
        if ($data['content_mode'] === 'multi' && !empty($data['pages'])) {
            $data['pages'] = $this->handlePageMedia($request, $data['pages']);
        }

        // Clear irrelevant field
        if ($data['content_mode'] === 'multi') {
            $data['content'] = null;
        } else {
            $data['pages'] = null;
        }

        Section::create($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil dibuat.');
    }

    public function edit(Section $section)
    {
        $courseTypes = CourseType::active()->get();
        return view('admin.sections.edit', compact('section', 'courseTypes'));
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'course_type_id'          => 'nullable|exists:course_types,id',
            'title'                   => 'required|string|max:255',
            'description'             => 'nullable|string',
            'content_mode'            => 'required|in:single,multi',
            'content'                 => 'nullable|string',
            'pages'                   => 'nullable|array',
            'pages.*.title'           => 'nullable|string|max:255',
            'pages.*.content'         => 'nullable|string',
            'pages.*.image_url'       => 'nullable|string',
            'pages.*.audio_url'       => 'nullable|string',
            'pages.*.new_audio'       => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac,audio/x-wav|max:51200',
            'media_type'              => 'required|in:video_upload,audio_upload,youtube,drive',
            'media_url'               => 'nullable|url',
            'video_file'              => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime|max:204800',
            'audio_file'              => 'nullable|file|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac,audio/x-wav|max:51200',
            'thumbnail'               => 'nullable|image|max:5120',
            'order'                   => 'nullable|integer|min:0',
            'is_published'            => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($section->thumbnail) {
                Storage::disk('public')->delete($section->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('sections/thumbnails', 'public');
        }

        // Hapus file lama jika diganti dengan file baru
        if ($request->hasFile('video_file') || $request->hasFile('audio_file')) {
            if ($section->media_file && in_array($section->media_type, ['video_upload', 'audio_upload'])) {
                Storage::disk('public')->delete($section->media_file);
            }
        }

        $this->handleMediaUpload($request, $data);

        // Handle multi-page images & audio upload
        // Hapus audio lama per slide jika diganti
        if ($data['content_mode'] === 'multi' && !empty($data['pages'])) {
            $oldPages = $section->pages ?? [];
            $data['pages'] = $this->handlePageMedia($request, $data['pages'], $oldPages);
        }

        // Jika mode berubah dari multi ke single, hapus semua media per-slide
        if ($data['content_mode'] === 'single' && $section->content_mode === 'multi') {
            $this->deleteAllPageMedia($section->pages ?? []);
        }

        // Clear irrelevant field
        if ($data['content_mode'] === 'multi') {
            $data['content'] = null;
        } else {
            $data['pages'] = null;
        }

        $section->update($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil diperbarui.');
    }

    public function destroy(Section $section)
    {
        if ($section->thumbnail) {
            Storage::disk('public')->delete($section->thumbnail);
        }
        if ($section->media_file && in_array($section->media_type, ['video_upload', 'audio_upload'])) {
            Storage::disk('public')->delete($section->media_file);
        }

        // Hapus gambar & audio tiap page jika ada
        $this->deleteAllPageMedia($section->pages ?? []);

        $section->delete();

        return redirect()->route('admin.sections.index')->with('success', 'Section berhasil dihapus.');
    }

    public function togglePublish(Section $section)
    {
        $section->update(['is_published' => ! $section->is_published]);
        $msg = $section->is_published ? 'Section berhasil dipublikasikan.' : 'Section berhasil disembunyikan.';
        return back()->with('success', $msg);
    }

    /**
     * Proses upload file media atau simpan URL, sesuai media_type.
     */
    private function handleMediaUpload(Request $request, array &$data): void
    {
        $type = $data['media_type'] ?? $request->input('media_type');

        if ($type === 'video_upload' && $request->hasFile('video_file')) {
            $data['media_file'] = $request->file('video_file')->store('sections/videos', 'public');
            $data['media_url']  = null;
        } elseif ($type === 'audio_upload' && $request->hasFile('audio_file')) {
            $data['media_file'] = $request->file('audio_file')->store('sections/audios', 'public');
            $data['media_url']  = null;
        } elseif (in_array($type, ['youtube', 'drive'])) {
            $data['media_url']  = $request->input('media_url');
            $data['media_file'] = null;
        }
    }

    /**
     * Proses upload gambar DAN audio per page di multi-page mode.
     * Mendukung replace audio lama jika ada file baru.
     */
    private function handlePageMedia(Request $request, array $pages, array $oldPages = []): array
    {
        $uploadedFiles = $request->file('pages') ?? [];

        foreach ($pages as $idx => &$page) {
            // ── Gambar per page ──────────────────────────────────────────────
            if (isset($uploadedFiles[$idx]['new_image']) && $uploadedFiles[$idx]['new_image']->isValid()) {
                $path = $uploadedFiles[$idx]['new_image']->store('sections/pages', 'public');
                $page['image_url']  = Storage::disk('public')->url($path);
                $page['image_path'] = $path;
            }
            unset($page['new_image']);

            // ── Audio per page ───────────────────────────────────────────────
            if (isset($uploadedFiles[$idx]['new_audio']) && $uploadedFiles[$idx]['new_audio']->isValid()) {
                // Hapus audio lama jika ada
                $oldAudioPath = $oldPages[$idx]['audio_path'] ?? null;
                if ($oldAudioPath) {
                    Storage::disk('public')->delete($oldAudioPath);
                }
                $audioPath = $uploadedFiles[$idx]['new_audio']->store('sections/pages/audios', 'public');
                $page['audio_url']  = Storage::disk('public')->url($audioPath);
                $page['audio_path'] = $audioPath;
            }

            // Hapus audio slide jika admin centang hapus
            if (!empty($page['remove_audio'])) {
                $oldAudioPath = $oldPages[$idx]['audio_path'] ?? null;
                if ($oldAudioPath) {
                    Storage::disk('public')->delete($oldAudioPath);
                }
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
            if (!empty($page['image_path'])) {
                Storage::disk('public')->delete($page['image_path']);
            }
            if (!empty($page['audio_path'])) {
                Storage::disk('public')->delete($page['audio_path']);
            }
        }
    }
}
