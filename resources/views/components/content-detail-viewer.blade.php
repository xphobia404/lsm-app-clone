@props([
    'content',
    'section',
])

<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

<style>
    /* Reset Quill border & padding untuk mode read-only */
    #content-body-viewer .ql-container.ql-snow { border: none !important; }
    #content-body-viewer .ql-editor             { padding: 0 !important; font-size: 0.875rem; color: #334155; cursor: default; }
    #content-body-viewer .ql-editor:focus       { outline: none; }
    /* Skeleton shimmer */
    @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
    .skeleton-bar {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s ease-in-out infinite;
        border-radius: 0.375rem;
        height: 1rem;
    }
</style>

<div class="px-4 pt-5 pb-10 space-y-5">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('admin.sections.index') }}" class="hover:text-indigo-600 transition">Sections</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('admin.sections.contents.index', $section) }}" class="hover:text-indigo-600 transition truncate">
            {{ $section->title }}
        </a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-800 font-semibold truncate">{{ $content->title }}</span>
    </nav>

    {{-- Header: judul + badge + tombol edit --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-800">{{ $content->title }}</h2>
            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                @php
                    $typeColor = match($content->content_type) {
                        'video' => 'bg-rose-50 text-rose-600',
                        'file'  => 'bg-amber-50 text-amber-600',
                        'url'   => 'bg-sky-50 text-sky-600',
                        default => 'bg-indigo-50 text-indigo-600',
                    };
                    $typeLabel = match($content->content_type) {
                        'video' => 'Video',
                        'file'  => 'File',
                        'url'   => 'URL',
                        default => 'Text',
                    };
                @endphp
                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $typeColor }}">
                    {{ $typeLabel }}
                </span>
                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold
                    {{ $content->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $content->is_active ? 'Aktif' : 'Non-aktif' }}
                </span>
                <span class="text-[10px] text-slate-400">Urutan: {{ $content->content_order }}</span>
            </div>
        </div>

        <a href="{{ route('admin.sections.contents.edit', [$section, $content]) }}"
           class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-100 transition shrink-0">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
    </div>

    {{-- ─── Isi Konten (Quill Read-Only) ─────────────────────────────────── --}}
    @if($content->body)
        <div class="rounded-2xl bg-white shadow-sm p-5">
            <h3 class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">Isi Konten</h3>

            <div id="content-body-skeleton" class="space-y-2">
                <div class="skeleton-bar w-full"></div>
                <div class="skeleton-bar w-5/6"></div>
                <div class="skeleton-bar w-4/6"></div>
            </div>

            <div id="content-body-viewer" class="hidden"></div>
        </div>
    @endif

    {{-- ─── URL Konten ──────────────────────────────────────────────────── --}}
    @if($content->url)
        <div class="rounded-2xl bg-white shadow-sm p-5">
            <h3 class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">URL</h3>
            <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer"
               class="text-sm text-indigo-600 break-all hover:underline">{{ $content->url }}</a>
        </div>
    @endif

    {{-- ─── Media Lampiran ─────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-white shadow-sm p-5">
        <h3 class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">
            Media Lampiran
            <span class="ml-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-600">
                {{ $content->media->count() }}
            </span>
        </h3>

        @if($content->media->isEmpty())
            <p class="text-xs text-slate-400 italic">Belum ada media untuk konten ini.</p>
        @else
            <div class="space-y-3">
                @foreach($content->media as $m)
                    @php
                        $mColor = match($m->media_type) {
                            'video' => ['bg' => 'bg-rose-50',   'text' => 'text-rose-600',   'badge' => 'bg-rose-100 text-rose-600'],
                            'audio' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'badge' => 'bg-violet-100 text-violet-600'],
                            'url'   => ['bg' => 'bg-sky-50',    'text' => 'text-sky-600',    'badge' => 'bg-sky-100 text-sky-600'],
                            default => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'badge' => 'bg-indigo-100 text-indigo-600'],
                        };
                    @endphp
                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">

                        {{-- Icon --}}
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $mColor['bg'] }}">
                            @if($m->media_type === 'image')
                                <svg class="h-4 w-4 {{ $mColor['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>
                                </svg>
                            @elseif($m->media_type === 'video')
                                <svg class="h-4 w-4 {{ $mColor['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                </svg>
                            @elseif($m->media_type === 'audio')
                                <svg class="h-4 w-4 {{ $mColor['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                            @else
                                <svg class="h-4 w-4 {{ $mColor['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                                <p class="text-xs font-semibold text-slate-700 truncate">
                                    {{ $m->title ?: '(' . strtoupper($m->media_type) . ')' }}
                                </p>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $mColor['badge'] }}">
                                    {{ strtoupper($m->media_type) }}
                                </span>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold
                                    {{ $m->is_active ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $m->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                                <span class="text-[10px] text-slate-400">#{{ $m->media_order }}</span>
                            </div>
                            @if($m->description)
                                <p class="text-[11px] text-slate-400 line-clamp-1 mb-1">{{ $m->description }}</p>
                            @endif
                            @if($m->url)
                                <a href="{{ $m->url }}" target="_blank" rel="noopener noreferrer"
                                   class="text-[11px] text-indigo-500 break-all hover:underline">{{ $m->url }}</a>
                            @endif
                            @if($m->file_path)
                                <div class="flex items-center gap-1.5 mt-1">
                                    <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    <a href="{{ Storage::url($m->file_path) }}" target="_blank"
                                       class="text-[11px] text-indigo-500 hover:underline truncate">{{ basename($m->file_path) }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ─── Meta ──────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-xs text-slate-400 space-y-1">
        <p>Dibuat: {{ $content->created_at->translatedFormat('d F Y, H:i') }}</p>
        <p>Diperbarui: {{ $content->updated_at->translatedFormat('d F Y, H:i') }}</p>
    </div>

</div>

{{-- ─── Quill Read-Only Init (UMD global, sama seperti create/edit) ────── --}}
@if($content->body)
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
    (function () {
        var skeleton = document.getElementById('content-body-skeleton');
        var viewer   = document.getElementById('content-body-viewer');

        if (!viewer) return;

        var quill = new Quill(viewer, {
            theme:    'snow',
            readOnly: true,
            modules:  { toolbar: false },
        });

        var htmlBody = @json($content->body);
        var delta    = quill.clipboard.convert({ html: htmlBody });
        quill.setContents(delta, 'silent');

        if (skeleton) skeleton.style.display = 'none';
        viewer.classList.remove('hidden');
    })();
</script>
@endif
