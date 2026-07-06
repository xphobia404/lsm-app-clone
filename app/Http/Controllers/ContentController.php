<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Media;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    // ── ADMIN ────────────────────────────────────────────────────────────

    public function index(Request $request, Section $section)
    {
        $contents = $section->contents()
            ->when($request->filled('search'), fn ($q) =>
                $q->where('title', 'like', "%{$request->search}%")
            )
            ->when($request->filled('status'), fn ($q) =>
                $q->where('is_active', $request->status === 'active')
            )
            ->when($request->filled('type'), fn ($q) =>
                $q->where('content_type', $request->type)
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
            'title'                     => 'required|string|max:255',
            'content_type'              => 'required|in:text,video,file,url',
            'body'                      => 'nullable|string',
            'url'                       => 'nullable|url|max:2000',
            'content_order'             => 'nullable|integer|min:0',
            'is_active'                 => 'sometimes|boolean',
            // media array
            'media'                     => 'nullable|array',
            'media.*.media_type'        => 'required|in:image,video,audio,url',
            'media.*.title'             => 'nullable|string|max:255',
            'media.*.description'       => 'nullable|string',
            'media.*.url'               => 'nullable|url|max:2000',
            'media.*.file'              => 'nullable|file|max:51200', // 50 MB
            'media.*.media_order'       => 'nullable|integer|min:0',
            'media.*.is_active'         => 'sometimes|boolean',
        ]);

        $validated['content_order'] = $validated['content_order']
            ?? ($section->contents()->max('content_order') + 1);
        $validated['is_active'] = $request->boolean('is_active');

        $content = $section->contents()->create($validated);

        // Simpan media
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
            'title'                     => 'required|string|max:255',
            'content_type'              => 'required|in:text,video,file,url',
            'body'                      => 'nullable|string',
            'url'                       => 'nullable|url|max:2000',
            'content_order'             => 'nullable|integer|min:0',
            'is_active'                 => 'sometimes|boolean',
            // media array
            'media'                     => 'nullable|array',
            'media.*.id'                => 'nullable|integer|exists:media,id',
            'media.*.media_type'        => 'required|in:image,video,audio,url',
            'media.*.title'             => 'nullable|string|max:255',
            'media.*.description'       => 'nullable|string',
            'media.*.url'               => 'nullable|url|max:2000',
            'media.*.file'              => 'nullable|file|max:51200',
            'media.*.media_order'       => 'nullable|integer|min:0',
            'media.*.is_active'         => 'sometimes|boolean',
            // ids media yang dihapus
            'deleted_media'             => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $content->update($validated);

        // Hapus media yang ditandai dihapus
        if ($request->filled('deleted_media')) {
            $deletedIds = array_filter(explode(',', $request->deleted_media));
            foreach ($deletedIds as $mid) {
                $m = Media::find($mid);
                if ($m && $m->mediable_id === $content->id && $m->mediable_type === Content::class) {
                    if ($m->file_path) Storage::disk('public')->delete($m->file_path);
                    $m->delete();
                }
            }
        }

        // Update / tambah media
        $this->syncMedia($request, $content);

        return redirect()
            ->route('admin.sections.contents.index', $section)
            ->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy(Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);

        // Hapus file media dari storage
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
        $label = $content->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Konten berhasil {$label}.");
    }

    // ── USER-FACING ─────────────────────────────────────────────────

    public function userShow(Section $section, Content $content)
    {
        abort_if(! $section->is_active || ! $content->is_active, 404);
        $this->authorizeContent($section, $content);

        $content->load(['media' => fn ($q) => $q->where('is_active', true)->orderBy('media_order')]);

        $allContents = $section->contents()->active()->orderBy('content_order')->get(['id', 'title', 'content_order']);
        $currentIndex = $allContents->search(fn ($c) => $c->id === $content->id);
        $prev = $currentIndex > 0 ? $allContents[$currentIndex - 1] : null;
        $next = $currentIndex < $allContents->count() - 1 ? $allContents[$currentIndex + 1] : null;

        $user = auth()->user();
        $progress = $user->progresses()
            ->firstOrCreate(
                ['section_id' => $section->id],
                ['status' => 'in_progress', 'started_at' => now()]
            );
        if ($progress->status === 'not_started') {
            $progress->update(['status' => 'in_progress', 'started_at' => now()]);
        }

        return view('user.contents.show', compact('section', 'content', 'prev', 'next', 'progress'));
    }

    // ── PRIVATE ─────────────────────────────────────────────────────

    private function authorizeContent(Section $section, Content $content): void
    {
        abort_if($content->section_id !== $section->id, 404);
    }

    private function syncMedia(Request $request, Content $content): void
    {
        if (! $request->has('media')) return;

        foreach ($request->input('media', []) as $index => $row) {
            $existingId = $row['id'] ?? null;
            $filePath   = null;

            // Handle file upload
            if ($request->hasFile("media.{$index}.file")) {
                $file     = $request->file("media.{$index}.file");
                $filePath = $file->store('contents/media', 'public');

                // Hapus file lama jika update
                if ($existingId) {
                    $old = Media::find($existingId);
                    if ($old && $old->file_path) {
                        Storage::disk('public')->delete($old->file_path);
                    }
                }
            }

            $payload = [
                'mediable_type' => Content::class,
                'mediable_id'   => $content->id,
                'media_type'    => $row['media_type'],
                'title'         => $row['title'] ?? null,
                'description'   => $row['description'] ?? null,
                'url'           => $row['url'] ?? null,
                'media_order'   => $row['media_order'] ?? $index,
                'is_active'     => isset($row['is_active']) ? (bool) $row['is_active'] : true,
            ];

            if ($filePath) $payload['file_path'] = $filePath;

            if ($existingId) {
                Media::where('id', $existingId)->update($payload);
            } else {
                Media::create($payload);
            }
        }
    }
}
