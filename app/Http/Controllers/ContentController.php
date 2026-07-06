<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Media;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    // ── ADMIN ────────────────────────────────────────────────────────────────

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
        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'body'                 => 'nullable|string',
            'content_order'        => 'nullable|integer|min:0',
            'is_active'            => 'sometimes|boolean',
            'media'                => 'nullable|array',
            'media.*.media_type'   => 'required|in:image,video,audio,youtube,google_drive',
            'media.*.title'        => 'nullable|string|max:255',
            'media.*.description'  => 'nullable|string',
            'media.*.url'          => 'nullable|max:2000',
            'media.*.file'         => 'nullable|file|max:204800', // 200 MB
            'media.*.media_order'  => 'nullable|integer|min:0',
            'media.*.is_active'    => 'sometimes|boolean',
        ]);

        $validated['content_order'] = $validated['content_order']
            ?? ($section->contents()->max('content_order') + 1);
        $validated['is_active'] = $request->boolean('is_active');

        $content = $section->contents()->create($validated);

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

        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'body'                 => 'nullable|string',
            'content_order'        => 'nullable|integer|min:0',
            'is_active'            => 'sometimes|boolean',
            'media'                => 'nullable|array',
            'media.*.id'           => 'nullable|integer|exists:media,id',
            'media.*.media_type'   => 'required|in:image,video,audio,youtube,google_drive',
            'media.*.title'        => 'nullable|string|max:255',
            'media.*.description'  => 'nullable|string',
            'media.*.url'          => 'nullable|max:2000',
            'media.*.file'         => 'nullable|file|max:204800', // 200 MB
            'media.*.media_order'  => 'nullable|integer|min:0',
            'media.*.is_active'    => 'sometimes|boolean',
            'deleted_media'        => 'nullable|string',
        ]);

        $content->update([
            'title'         => $validated['title'],
            'body'          => $validated['body'] ?? null,
            'content_order' => $validated['content_order'] ?? $content->content_order,
            'is_active'     => $request->boolean('is_active'),
        ]);

        // Hapus media yang ditandai dihapus
        if (! empty($validated['deleted_media'])) {
            foreach (array_filter(explode(',', $validated['deleted_media'])) as $id) {
                $m = Media::find($id);
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

    // ── PRIVATE ──────────────────────────────────────────────────────────────

    private function authorizeContent(Section $section, Content $content): void
    {
        abort_if($content->section_id !== $section->id, 404);
    }

    private function syncMedia(Request $request, Content $content): void
    {
        if (! $request->filled('media')) return;

        $urlTypes = ['youtube', 'google_drive'];

        foreach ($request->input('media', []) as $idx => $data) {
            $type    = $data['media_type'];
            $isUrl   = in_array($type, $urlTypes);

            $payload = [
                'media_type'  => $type,
                'title'       => $data['title']       ?? null,
                'description' => $data['description'] ?? null,
                'url'         => $isUrl ? ($data['url'] ?? null) : null,
                'media_order' => $data['media_order']  ?? $idx,
                'is_active'   => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            ];

            if (! $isUrl && $request->hasFile("media.{$idx}.file")) {
                $path = $request->file("media.{$idx}.file")->store('media', 'public');
                $payload['file_path'] = $path;
            }

            if (! empty($data['id'])) {
                $existing = Media::find($data['id']);
                if ($existing) {
                    if (! $isUrl && isset($payload['file_path']) && $existing->file_path) {
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
