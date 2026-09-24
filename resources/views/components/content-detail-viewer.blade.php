@props([
    'content',
    'section'  => null,
    'mode'     => 'admin',  // 'admin' | 'user'
])

@php
    $vid   = 'ql-viewer-' . $content->id;
    $skel  = 'ql-skel-'   . $content->id;
    $media = $content->media ?? collect();

    $images   = $media->filter(fn($m) => $m->isImage());
    $videos   = $media->filter(fn($m) => $m->isVideo());
    $audios   = $media->filter(fn($m) => $m->isAudio());
    $youtubes = $media->filter(fn($m) => $m->isYouTube());
    $drives   = $media->filter(fn($m) => $m->isGoogleDrive());

    $hasImage = $images->isNotEmpty();
    $rawBody  = $content->body ?? '';
    $hasBody  = !empty(trim($rawBody)) && (
        trim(strip_tags($rawBody)) !== ''
        || str_contains($rawBody, '<table')
        || str_contains($rawBody, '<img')
        || str_contains($rawBody, '<iframe')
        || str_contains($rawBody, '<video')
    );

    $embedVideo = function(string $url): string {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/|youtube\.com\/embed\/)+([\w-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&playsinline=1';
        }
        return $url;
    };

    $embedDrive = function(string $url): string {
        if (str_contains($url, '/preview')) return $url;
        if (preg_match('#drive\.google\.com/file/d/([\w-]+)#', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }
        if (preg_match('#[?&]id=([\w-]+)#', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }
        return $url;
    };

    $isUploadedVideo = function(string $url): bool {
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'm4v']);
    };

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

{{-- Quill CSS — load sekali, idempotent via id check di JS --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

<style>
.ql-snippet-body {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
}

.ql-snippet-body .ql-container.ql-snow,
.ql-ro-viewer .ql-container.ql-snow { border: none !important; }

.ql-snippet-body .ql-editor,
.ql-ro-viewer .ql-editor {
    padding: 0 !important;
    font-size: 0.875rem;
    color: #334155;
    cursor: default;
    line-height: 1.7;
    word-break: break-word;
    overflow-wrap: break-word;
    min-width: 0 !important;
    width: 100% !important;
}
.ql-snippet-body .ql-editor:focus,
.ql-ro-viewer .ql-editor:focus { outline: none; }

/* Responsive Media inside Quill Editor Body */
.ql-editor img {
    max-width: 100% !important;
    height: auto !important;
    border-radius: 0.75rem;
    margin: 0.5rem 0;
    object-fit: contain;
    display: block;
}

.ql-editor iframe,
.ql-editor video {
    max-width: 100% !important;
    border-radius: 0.75rem;
}

.ql-editor pre,
.ql-editor pre.ql-syntax {
    max-width: 100% !important;
    white-space: pre-wrap !important;
    word-break: break-all !important;
    overflow-x: auto !important;
    background: #0f172a;
    color: #f8fafc;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    font-size: 0.8125rem;
}

/* Table responsive container & styling */
.ql-ro-viewer .table-responsive,
.ql-editor .table-responsive,
.table-responsive {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 0.75rem 0;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.ql-ro-viewer .ql-editor table,
.ql-editor table {
    width: 100% !important;
    min-width: 100% !important;
    table-layout: auto !important;
    border-collapse: collapse !important;
    font-size: 0.8125rem !important;
    line-height: 1.5 !important;
    color: #334155 !important;
    margin: 0 !important;
}

.ql-ro-viewer .ql-editor table th,
.ql-ro-viewer .ql-editor table td,
.ql-editor table th,
.ql-editor table td {
    padding: 0.5rem 0.625rem !important;
    border: 1px solid #cbd5e1 !important;
    min-width: 70px !important;
    vertical-align: top !important;
    word-break: normal !important;
    box-sizing: border-box !important;
}

.ql-ro-viewer .ql-editor table th,
.ql-editor table th {
    background-color: #f8fafc !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    text-align: left !important;
}

.ql-ro-viewer .ql-editor table tr:nth-child(even),
.ql-editor table tr:nth-child(even) {
    background-color: rgba(248, 250, 252, 0.6);
}
</style>

<div class="space-y-4">

    {{-- ── ADMIN ONLY: breadcrumb + header + edit button ── --}}
    @if($mode === 'admin' && $section)
    <nav class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('admin.sections.index') }}" class="hover:text-indigo-600 transition">Sections</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.sections.contents.index', $section) }}" class="hover:text-indigo-600 transition truncate">{{ $section->title }}</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-semibold truncate">{{ $content->title }}</span>
    </nav>

    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-800">{{ $content->title }}</h2>
            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $typeColor }}">{{ $typeLabel }}</span>
                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $content->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $content->is_active ? 'Aktif' : 'Non-aktif' }}
                </span>
                <span class="text-[10px] text-slate-400">Urutan: {{ $content->content_order }}</span>
            </div>
        </div>
        <a href="{{ route('admin.sections.contents.edit', [$section, $content]) }}"
           class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-100 transition shrink-0">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
    </div>
    @endif

    {{-- ── VIDEO (content_type = video atau dari media) ── --}}
    @php
        $vRaw = '';
        if ($content->isVideo() && $content->url) {
            $vRaw = $content->url;
        } elseif ($videos->isNotEmpty()) {
            $vRaw = $videos->first()->getDisplayUrl() ?? '';
        } elseif ($content->isVideo() && $content->body) {
            $vRaw = $content->body;
        }
    @endphp
    @if($vRaw)
        @php $vEmbed = $embedVideo($vRaw); @endphp
        <div class="embed-wrap">
            @if($vEmbed !== $vRaw)
                <iframe src="{{ $vEmbed }}" frameborder="0" allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
            @elseif($isUploadedVideo($vRaw))
                <video controls playsinline preload="metadata" class="w-full" style="border-radius:14px">
                    <source src="{{ $vRaw }}">Browser tidak mendukung video.
                </video>
            @else
                <iframe src="{{ $vRaw }}" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
            @endif
        </div>
    @endif

    {{-- ── MEDIA VIDEO list ── --}}
    @foreach($videos as $mv)
    @php $mvSrc = $mv->getDisplayUrl() ?? ''; $mvEmbed = $mvSrc ? $embedVideo($mvSrc) : ''; @endphp
    @if($mvSrc && $mvSrc !== $vRaw)
        <div>
            @if($mv->title)<p class="mb-1.5 text-xs font-semibold text-slate-600">🎬 {{ $mv->title }}</p>@endif
            @if($mvEmbed !== $mvSrc)
                <div class="embed-wrap"><iframe src="{{ $mvEmbed }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe></div>
            @elseif($isUploadedVideo($mvSrc))
                <video controls playsinline preload="metadata" class="w-full" style="border-radius:14px; max-height:55vw; object-fit:contain">
                    <source src="{{ $mvSrc }}">Browser tidak mendukung video.
                </video>
            @endif
            @if($mv->description)<p class="mt-1 text-[10px] text-slate-400">{{ $mv->description }}</p>@endif
        </div>
    @endif
    @endforeach

    {{-- ── YOUTUBE MEDIA ── --}}
    @foreach($youtubes as $yt)
    @php
        $ytEmbed = method_exists($yt,'getYouTubeEmbedUrl') ? $yt->getYouTubeEmbedUrl() : $embedVideo($yt->url ?? '');
        if ($ytEmbed && !str_contains($ytEmbed,'playsinline')) $ytEmbed .= (str_contains($ytEmbed,'?') ? '&' : '?') . 'rel=0&playsinline=1';
    @endphp
    @if($ytEmbed)
    <div>
        @if($yt->title)
        <p class="mb-1.5 text-xs font-semibold text-slate-600 flex items-center gap-1">
            <svg class="h-3.5 w-3.5 text-red-500" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            {{ $yt->title }}
        </p>
        @endif
        <div class="embed-wrap"><iframe src="{{ $ytEmbed }}" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin"></iframe></div>
        @if($yt->description)<p class="mt-1.5 text-[10px] text-slate-400 leading-relaxed">{{ $yt->description }}</p>@endif
    </div>
    @endif
    @endforeach

    {{-- ── GOOGLE DRIVE ── --}}
    @foreach($drives as $drive)
    @php
        $driveRaw   = method_exists($drive,'getGoogleDriveEmbedUrl') ? $drive->getGoogleDriveEmbedUrl() : ($drive->url ?? '');
        $driveEmbed = $driveRaw ? $embedDrive($driveRaw) : '';
    @endphp
    @if($driveEmbed)
    <div>
        @if($drive->title)
        <p class="mb-1.5 text-xs font-semibold text-slate-600 flex items-center gap-1">
            <svg class="h-3.5 w-3.5 text-blue-500" viewBox="0 0 24 24" fill="currentColor"><path d="M4.433 22.396l4-6.929H24l-4 6.929H4.433zm3.566-6.929L2.566 6.536l4-6.929 5.434 9.403-4.001 6.857zM13.567 8.01L18.992.107 24 9.01H13.567z"/></svg>
            {{ $drive->title }}
        </p>
        @endif
        <div class="embed-wrap" style="padding-top:75%"><iframe src="{{ $driveEmbed }}" frameborder="0" allowfullscreen allow="autoplay" sandbox="allow-scripts allow-same-origin allow-popups allow-forms"></iframe></div>
        @if($drive->description)<p class="mt-1.5 text-[10px] text-slate-400 leading-relaxed">{{ $drive->description }}</p>@endif
    </div>
    @endif
    @endforeach

    {{-- ── GAMBAR + BODY / GAMBAR SAJA / BODY SAJA ── --}}
    @if($hasImage && $hasBody)
        <div class="media-split">
            <div class="media-split__img">
                @foreach($images as $img)
                <div class="media-split__img-wrap" style="{{ !$loop->first ? 'margin-top:8px' : '' }}">
                    <img src="{{ $img->getDisplayUrl() }}" alt="{{ $img->title ?? $content->title }}" loading="lazy">
                </div>
                @if($img->description)<p class="mt-1 text-[10px] text-slate-400 text-center leading-tight">{{ $img->description }}</p>@endif
                @endforeach
            </div>
            <div class="media-split__text">
                <div class="ql-snippet-body ql-container ql-snow border-0">
                    <div class="ql-editor p-0">
                        {!! $content->body !!}
                    </div>
                </div>
            </div>
        </div>

    @elseif($hasImage && !$hasBody)
        <div class="space-y-2">
            @foreach($images as $img)
            <div class="media-img-only">
                <img src="{{ $img->getDisplayUrl() }}" alt="{{ $img->title ?? $content->title }}" loading="lazy">
            </div>
            @if($img->description)<p class="text-[10px] text-slate-400 text-center leading-tight">{{ $img->description }}</p>@endif
            @endforeach
        </div>

    @elseif(!$hasImage && $hasBody && !$content->isUrl() && !$content->isFile())
        <div class="ql-snippet-body ql-container ql-snow border-0">
            <div class="ql-editor p-0">
                {!! $content->body !!}
            </div>
        </div>
    @endif

    {{-- ── URL ── --}}
    @if($content->isUrl())
    <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer"
       class="flex items-center gap-3 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100">
            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-indigo-700">Buka Tautan</p>
            <p class="truncate text-[10px] text-indigo-400">{{ $content->url }}</p>
        </div>
    </a>
    @if($hasBody)
    <div class="ql-snippet-body ql-container ql-snow border-0 mt-3">
        <div class="ql-editor p-0">
            {!! $content->body !!}
        </div>
    </div>
    @endif
    @endif

    {{-- ── FILE ── --}}
    @if($content->isFile())
    <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer"
       class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white shadow-sm">
            <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-700">Unduh File</p>
            <p class="text-[10px] text-slate-400">Tap untuk membuka</p>
        </div>
    </a>
    @if($hasBody)
    <div class="ql-snippet-body ql-container ql-snow border-0 mt-3">
        <div class="ql-editor p-0">
            {!! $content->body !!}
        </div>
    </div>
    @endif
    @endif

    {{-- ── AUDIO ── --}}
    @if($audios->isNotEmpty())
    <div class="space-y-3">
        @foreach($audios as $audio)
        @php $audioSrc = $audio->getDisplayUrl(); @endphp
        @if($audioSrc)
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            @if($audio->title)<p class="mb-2 text-xs font-semibold text-slate-600">🎵 {{ $audio->title }}</p>@endif
            <audio controls class="w-full" style="height:40px; border-radius:8px">
                <source src="{{ $audioSrc }}">Browser tidak mendukung audio.
            </audio>
            @if($audio->description)<p class="mt-1 text-[10px] text-slate-400">{{ $audio->description }}</p>@endif
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- ── ADMIN ONLY: media lampiran list + meta ── --}}
    @if($mode === 'admin')
    <div class="rounded-2xl bg-white shadow-sm p-5">
        <h3 class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">
            Media Lampiran
            <span class="ml-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-600">{{ $media->count() }}</span>
        </h3>
        @if($media->isEmpty())
            <p class="text-xs text-slate-400 italic">Belum ada media untuk konten ini.</p>
        @else
            <div class="space-y-3">
                @foreach($media as $m)
                @php
                    $mc = match($m->media_type) {
                        'video' => ['bg'=>'bg-rose-50','text'=>'text-rose-600','badge'=>'bg-rose-100 text-rose-600'],
                        'audio' => ['bg'=>'bg-violet-50','text'=>'text-violet-600','badge'=>'bg-violet-100 text-violet-600'],
                        'url'   => ['bg'=>'bg-sky-50','text'=>'text-sky-600','badge'=>'bg-sky-100 text-sky-600'],
                        default => ['bg'=>'bg-indigo-50','text'=>'text-indigo-600','badge'=>'bg-indigo-100 text-indigo-600'],
                    };
                @endphp
                <div class="flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $mc['bg'] }}">
                        @if($m->media_type==='image')
                        <svg class="h-4 w-4 {{ $mc['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                        @elseif($m->media_type==='video')
                        <svg class="h-4 w-4 {{ $mc['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                        @elseif($m->media_type==='audio')
                        <svg class="h-4 w-4 {{ $mc['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                        @else
                        <svg class="h-4 w-4 {{ $mc['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                            <p class="text-xs font-semibold text-slate-700 truncate">{{ $m->title ?: '('.strtoupper($m->media_type).')' }}</p>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $mc['badge'] }}">{{ strtoupper($m->media_type) }}</span>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $m->is_active ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400' }}">{{ $m->is_active ? 'Aktif' : 'Non-aktif' }}</span>
                            <span class="text-[10px] text-slate-400">#{{ $m->media_order }}</span>
                        </div>
                        @if($m->description)<p class="text-[11px] text-slate-400 line-clamp-1 mb-1">{{ $m->description }}</p>@endif
                        @if($m->url)<a href="{{ $m->url }}" target="_blank" rel="noopener noreferrer" class="text-[11px] text-indigo-500 break-all hover:underline">{{ $m->url }}</a>@endif
                        @if($m->file_path)
                        <div class="flex items-center gap-1.5 mt-1">
                            <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <a href="{{ Storage::url($m->file_path) }}" target="_blank" class="text-[11px] text-indigo-500 hover:underline truncate">{{ basename($m->file_path) }}</a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-xs text-slate-400 space-y-1">
        <p>Dibuat: {{ $content->created_at->translatedFormat('d F Y, H:i') }}</p>
        <p>Diperbarui: {{ $content->updated_at->translatedFormat('d F Y, H:i') }}</p>
    </div>
    @endif

</div>

@once
<script>
(function(){
    function wrapTables() {
        document.querySelectorAll('.ql-editor table').forEach(function(tbl) {
            if (!tbl.parentElement || !tbl.parentElement.classList.contains('table-responsive')) {
                var wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                tbl.parentNode.insertBefore(wrapper, tbl);
                wrapper.appendChild(tbl);
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', wrapTables);
    } else {
        wrapTables();
    }
}());
</script>
@endonce
