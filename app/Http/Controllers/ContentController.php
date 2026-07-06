<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Section;
use Illuminate\Http\Request;

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
            'title'         => 'required|string|max:255',
            'content_type'  => 'required|in:text,video,file,url',
            'body'          => 'nullable|string',
            'url'           => 'nullable|url|max:2000',
            'content_order' => 'nullable|integer|min:0',
            'is_active'     => 'sometimes|boolean',
        ]);

        $validated['content_order'] = $validated['content_order']
            ?? ($section->contents()->max('content_order') + 1);
        $validated['is_active'] = $request->boolean('is_active');

        $section->contents()->create($validated);

        return redirect()
            ->route('admin.sections.contents.index', $section)
            ->with('success', 'Konten berhasil ditambahkan.');
    }

    public function show(Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);
        $content->load('media');
        return view('admin.contents.show', compact('section', 'content'));
    }

    public function edit(Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);
        return view('admin.contents.edit', compact('section', 'content'));
    }

    public function update(Request $request, Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'content_type'  => 'required|in:text,video,file,url',
            'body'          => 'nullable|string',
            'url'           => 'nullable|url|max:2000',
            'content_order' => 'nullable|integer|min:0',
            'is_active'     => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $content->update($validated);

        return redirect()
            ->route('admin.sections.contents.index', $section)
            ->with('success', 'Konten berhasil diperbarui.');
    }

    public function destroy(Section $section, Content $content)
    {
        $this->authorizeContent($section, $content);
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

    /**
     * Halaman baca konten oleh user.
     * Route: GET /app/sections/{section}/contents/{content}
     */
    public function userShow(Section $section, Content $content)
    {
        abort_if(! $section->is_active || ! $content->is_active, 404);
        $this->authorizeContent($section, $content);

        $content->load('media');

        // Ambil konten prev/next untuk navigasi
        $allContents = $section->contents()->active()->orderBy('content_order')->get(['id', 'title', 'content_order']);
        $currentIndex = $allContents->search(fn ($c) => $c->id === $content->id);
        $prev = $currentIndex > 0 ? $allContents[$currentIndex - 1] : null;
        $next = $currentIndex < $allContents->count() - 1 ? $allContents[$currentIndex + 1] : null;

        // Update progress user
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
}
