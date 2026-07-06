<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Media;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    // ── ADMIN ────────────────────────────────────────────────────────

    public function index(Request $request, Section $section)
    {
        $contents = $section->contents()
            ->when($request->filled('search'), fn ($q) =>
                $q->where('title', 'like', "%{$request->search}%")
            )
            ->when($request->filled('status'), fn ($q) =>
                $q->where('is_active', $request->status === 'active')
            )
            ->withCount('media')
            ->orderBy('content_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.contents.index', compact('section', 'contents'));
    }

    public function create(Section $section)
    {
        return view('admin.contents.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'body'                => 'nullable|string',
            'content_order'       => 'nullable|integer|min:0',
            'is_active'           => 'sometimes|boolean',
            'media'               => 'nullable|array',
            'media.*.media_type'  => 'required|in:image,video,audio,youtube,google_drive',
            'media.*.title'       => 'nullable|string|max:255',
            'media.*.description' => 'nullable|string',
            'media.*.url'         => 'nullable|string|max:2000',
            'media.*.media_order' => 'nullable|integer|min:0',
            'media.*.is_active'   => 'sometimes|boolean',
            // Hanya batasi ukuran, TANPA mimes agar video/audio tidak diblokir
            'media.*.file'        => 'nullable|file|max:204800',
        ]);

        $order = $request->input('content_order')
            ?? ($section->contents()->max('content_order') + 1);

        $content = $section->contents()->create([
            'title'         => $request->input('title'),
            'body'          => $request->input('body'),
            'content_order' => $order,
            'is_active'     => $request->boolean('is_active'),
        ]);

        $this->syncMedia($request, $content);

        return redirect()
            ->route('admin.sections.contents.index', $section)
            ->with('success', 'Konten berhasil ditambahkan.');
    }

    public function show(Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);
        $content->load(['media' => fn ($q) => $q->orderBy('media_order')]);
        return view('admin.contents.show', compact('section', 'content'));
    }

    public function edit(Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);
        $content->load(['media' => fn ($q) => $q->orderBy('media_order')]);
        return view('admin.contents.edit', compact('section', 'content'));
    }

    public function update(Request $request, Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);

        $request->validate([
            'title'               => 'required|string|max:255',
            'body'                => 'nullable|string',
            'content_order'       => 'nullable|integer|min:0',
            'is_active'           => 'sometimes|boolean',
            'media'               => 'nullable|array',
            'media.*.id'          => 'nullable|integer|exists:media,id',
            'media.*.media_type'  => 'required|in:image,video,audio,youtube,google_drive',
            'media.*.title'       => 'nullable|string|max:255',
            'media.*.description' => 'nullable|string',
            'media.*.url'         => 'nullable|string|max:2000',
            'media.*.media_order' => 'nullable|integer|min:0',
            'media.*.is_active'   => 'sometimes|boolean',
            'media.*.file'        => 'nullable|file|max:204800',
            'deleted_media'       => 'nullable|string',
        ]);

        $content->update([
            'title'         => $request->input('title'),
            'body'          => $request->input('body'),
            'content_order' => $request->input('content_order', $content->content_order),
            'is_active'     => $request->boolean('is_active'),
        ]);

        if ($request->filled('deleted_media')) {
            foreach (array_filter(explode(',', $request->input('deleted_media'))) as $id) {
                $m = Media::find((int) $id);
                if ($m) {
                    if ($m->file_path) Storage::disk('public')->delete($m->file_path);
                    $m->delete();
                }
            }
        }

        $this->syncMedia($request, $content);

        return redirect()
            ->route('admin.sections.contents.index', $section)
            ->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy(Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);
        foreach ($content->media as $m) {
            if ($m->file_path) Storage::disk('public')->delete($m->file_path);
        }
        $content->delete();
        return redirect()
            ->route('admin.sections.contents.index', $section)
            ->with('success', 'Konten berhasil dihapus.');
    }

    public function toggleActive(Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);
        $content->update(['is_active' => ! $content->is_active]);
        return back()->with('success', 'Status konten diperbarui.');
    }

    // ── PRIVATE ─────────────────────────────────────────────────────

    private function authorizeContent(Section $section, Content $content): void
    {
        abort_if($content->section_id !== $section->id, 404);
    }

    private function syncMedia(Request $request, Content $content): void
    {
        $mediaInputs = $request->input('media', []);
        if (empty($mediaInputs)) return;

        /*
         * allFiles() mengembalikan semua file yang diupload dalam bentuk array nested.
         * Ini lebih reliable daripada $request->file('media.0.file') untuk nested input.
         * Struktur: $allFiles['media'][$idx]['file'] = UploadedFile
         */
        $allFiles = $request->allFiles();
        $urlTypes = ['youtube', 'google_drive'];

        foreach ($mediaInputs as $idx => $data) {
            $type  = $data['media_type'] ?? 'image';
            $isUrl = in_array($type, $urlTypes);

            $payload = [
                'media_type'  => $type,
                'title'       => $data['title']        ?? null,
                'description' => $data['description']  ?? null,
                'url'         => $isUrl ? ($data['url'] ?? null) : null,
                'media_order' => isset($data['media_order']) ? (int) $data['media_order'] : (int) $idx,
                'is_active'   => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            ];

            // Proses upload file untuk tipe non-URL
            if (! $isUrl) {
                $uploadedFile = $allFiles['media'][$idx]['file'] ?? null;
                if ($uploadedFile !== null && $uploadedFile->isValid()) {
                    $path = $uploadedFile->store('media', 'public');
                    $payload['file_path'] = $path;
                }
            }

            // Update media yang sudah ada
            if (! empty($data['id'])) {
                $existing = Media::find((int) $data['id']);
                if ($existing) {
                    if (isset($payload['file_path']) && $existing->file_path) {
                        Storage::disk('public')->delete($existing->file_path);
                    }
                    $existing->update($payload);
                    continue;
                }
            }

            $content->media()->create($payload);
        }
    }
}
