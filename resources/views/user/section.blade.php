{{-- resources/views/user/section.blade.php --}}
<x-reader-layout :title="$section->title">
@php
    $contents    = $section->contents;
    $quizzes     = $section->quizzes;
    $totalSlides = $contents->count() + ($quizzes->isNotEmpty() ? 1 : 0);
    $hasQuiz     = $quizzes->isNotEmpty();
    if ($totalSlides === 0) $totalSlides = 1;

    $contentIdsJs = $contents->pluck('id')->toJson();
@endphp

{{-- Quill CSS (load sekali) --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">

<style>
/* ── Quill read-only reset (user view) ── */
.ql-body-viewer .ql-container.ql-snow { border: none !important; }
.ql-body-viewer .ql-editor             { padding: 0 !important; font-size: 0.875rem; color: #334155; cursor: default; line-height: 1.7; }
.ql-body-viewer .ql-editor:focus       { outline: none; }

/* ── Content body fallback (jika body dirender raw) ── */
.content-body {
    min-width: 0;
    max-width: 100%;
    overflow-x: hidden;
}
.content-body > * { max-width: 100%; }
.content-body p,
.content-body div,
.content-body li,
.content-body blockquote,
.content-body h1,.content-body h2,.content-body h3,
.content-body h4,.content-body h5,.content-body h6 {
    white-space: normal;
    word-break: normal;
    overflow-wrap: break-word;
}
.content-body a,.content-body span,.content-body strong,
.content-body em,.content-body code {
    word-break: break-word;
    overflow-wrap: break-word;
}
.content-body img   { max-width: 100%; height: auto; border-radius: 8px; }
.content-body iframe { max-width: 100%; }
.content-body video,.content-body audio,.content-body embed,.content-body object { max-width: 100%; }
.content-body table { display: block; max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
.content-body pre   { max-width: 100%; overflow-x: auto; white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word; }
.content-body code  { white-space: pre-wrap; }
@media (max-width: 640px) {
    .content-body { font-size: 14px; line-height: 1.7; }
}

/* Skeleton shimmer */
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
.ql-skeleton-bar {
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
    border-radius: 0.375rem;
    height: 0.875rem;
}
</style>

<div class="flex flex-col" style="min-height:100dvh">

{{-- TOP BAR --}}
<div class="fixed top-0 left-0 right-0 z-50 flex items-center gap-3 border-b border-slate-100 bg-white px-4 py-3"
     style="backdrop-filter:blur(8px)">
    <a href="{{ route('user.schemas.show', $learningSchema) }}"
       class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="flex-1 min-w-0">
        <p class="truncate text-xs font-bold text-slate-800">{{ $section->title }}</p>
        <p class="text-[10px] text-slate-400">{{ $learningSchema->title }}</p>
    </div>
    <div class="shrink-0 flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1">
        <span id="slide-current" class="text-[11px] font-extrabold text-indigo-600">1</span>
        <span class="text-[10px] text-indigo-300">/</span>
        <span class="text-[11px] font-bold text-indigo-400">{{ $totalSlides }}</span>
    </div>
</div>

{{-- Progress bar --}}
<div class="fixed z-40 h-0.5 w-full bg-slate-100" style="top:53px">
    <div id="progress-bar" class="h-0.5 bg-indigo-500 transition-all duration-300"
         style="width:{{ round(1/$totalSlides*100) }}%"></div>
</div>

{{-- SLIDER AREA --}}
<div class="overflow-hidden flex-1" style="padding-top:57px; padding-bottom:80px">
    <div id="slides-track" class="flex transition-transform duration-300 ease-in-out"
         style="width:{{ $totalSlides * 100 }}%">

        @foreach($contents as $i => $content)
        @php
            $media    = $content->media ?? collect();
            $images   = $media->filter(fn($m) => $m->isImage());
            $videos   = $media->filter(fn($m) => $m->isVideo());
            $audios   = $media->filter(fn($m) => $m->isAudio());
            $youtubes = $media->filter(fn($m) => $m->isYouTube());
            $drives   = $media->filter(fn($m) => $m->isGoogleDrive());
            $hasImage = $images->isNotEmpty();
            $hasBody  = !empty(trim(strip_tags($content->body ?? '')));
            $bodyId   = 'ql-viewer-' . $content->id;
            $skelId   = 'ql-skel-'   . $content->id;

            $embedVideo = function(string $url): string {
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([\w-]+)/', $url, $m)) {
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
        @endphp

        <div class="slide-panel px-4 py-5" style="width:{{ round(100/$totalSlides,4) }}%; flex-shrink:0">

            {{-- Header --}}
            <div class="mb-4 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-extrabold text-indigo-600">{{ $i+1 }}</span>
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ $content->title }}</p>
                    <p class="text-[10px] text-slate-400 capitalize">{{ $content->content_type }}</p>
                </div>
            </div>

            {{-- VIDEO --}}
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
                @if($vEmbed !== $vRaw)
                    <div class="mb-4 embed-wrap">
                        <iframe src="{{ $vEmbed }}" frameborder="0" allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
                    </div>
                @elseif($isUploadedVideo($vRaw))
                    <div class="mb-4 video-upload-wrap">
                        <video controls playsinline preload="metadata">
                            <source src="{{ $vRaw }}">
                            Browser tidak mendukung video.
                        </video>
                    </div>
                @else
                    <div class="mb-4 embed-wrap">
                        <iframe src="{{ $vRaw }}" frameborder="0" allowfullscreen
                                allow="autoplay; encrypted-media"></iframe>
                    </div>
                @endif
            @endif

            {{-- MEDIA VIDEO --}}
            @foreach($videos as $vid)
            @php $vidSrc = $vid->getDisplayUrl() ?? ''; @endphp
            @if($vidSrc && $vidSrc !== $vRaw)
                @php $vEmbedMedia = $embedVideo($vidSrc); @endphp
                @if($vEmbedMedia !== $vidSrc)
                    <div class="mb-4 embed-wrap">
                        <iframe src="{{ $vEmbedMedia }}" frameborder="0" allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"></iframe>
                    </div>
                @elseif($isUploadedVideo($vidSrc))
                    <div class="mb-4 video-upload-wrap">
                        @if($vid->title)
                        <p class="mb-1.5 text-xs font-semibold text-slate-600">🎬 {{ $vid->title }}</p>
                        @endif
                        <video controls playsinline preload="metadata" class="w-full" style="border-radius:14px; max-height:55vw; object-fit:contain">
                            <source src="{{ $vidSrc }}">
                            Browser tidak mendukung video.
                        </video>
                        @if($vid->description)
                        <p class="mt-1 text-[10px] text-slate-400">{{ $vid->description }}</p>
                        @endif
                    </div>
                @endif
            @endif
            @endforeach

            {{-- YOUTUBE MEDIA --}}
            @foreach($youtubes as $yt)
            @php
                $ytEmbed = method_exists($yt, 'getYouTubeEmbedUrl')
                    ? $yt->getYouTubeEmbedUrl()
                    : $embedVideo($yt->url ?? '');
                if ($ytEmbed && !str_contains($ytEmbed, 'playsinline')) {
                    $ytEmbed .= (str_contains($ytEmbed, '?') ? '&' : '?') . 'rel=0&playsinline=1';
                }
            @endphp
            @if($ytEmbed)
            <div class="mb-4">
                @if($yt->title)
                <p class="mb-1.5 text-xs font-semibold text-slate-600 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-red-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                    {{ $yt->title }}
                </p>
                @endif
                <div class="embed-wrap">
                    <iframe src="{{ $ytEmbed }}" frameborder="0" allowfullscreen
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>
                @if($yt->description)
                <p class="mt-1.5 text-[10px] text-slate-400 leading-relaxed">{{ $yt->description }}</p>
                @endif
            </div>
            @endif
            @endforeach

            {{-- GOOGLE DRIVE MEDIA --}}
            @foreach($drives as $drive)
            @php
                $driveRaw   = method_exists($drive, 'getGoogleDriveEmbedUrl')
                    ? $drive->getGoogleDriveEmbedUrl()
                    : ($drive->url ?? '');
                $driveEmbed = $driveRaw ? $embedDrive($driveRaw) : '';
            @endphp
            @if($driveEmbed)
            <div class="mb-4">
                @if($drive->title)
                <p class="mb-1.5 text-xs font-semibold text-slate-600 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4.433 22.396l4-6.929H24l-4 6.929H4.433zm3.566-6.929L2.566 6.536l4-6.929 5.434 9.403-4.001 6.857zM13.567 8.01L18.992.107 24 9.01H13.567z"/>
                    </svg>
                    {{ $drive->title }}
                </p>
                @endif
                <div class="embed-wrap" style="padding-top:75%">
                    <iframe src="{{ $driveEmbed }}" frameborder="0" allowfullscreen
                            allow="autoplay"
                            sandbox="allow-scripts allow-same-origin allow-popups allow-forms"></iframe>
                </div>
                @if($drive->description)
                <p class="mt-1.5 text-[10px] text-slate-400 leading-relaxed">{{ $drive->description }}</p>
                @endif
            </div>
            @endif
            @endforeach

            {{-- ── GAMBAR + BODY QUILL / GAMBAR SAJA / BODY SAJA ── --}}
            @if($hasImage && $hasBody)
            <div class="mb-4 media-split">
                <div class="media-split__img">
                    @foreach($images as $img)
                    <div class="media-split__img-wrap" style="{{ !$loop->first ? 'margin-top:8px' : '' }}">
                        <img src="{{ $img->getDisplayUrl() }}" alt="{{ $img->title ?? $content->title }}" loading="lazy">
                    </div>
                    @if($img->description)
                    <p class="mt-1 text-[10px] text-slate-400 text-center leading-tight">{{ $img->description }}</p>
                    @endif
                    @endforeach
                </div>
                {{-- Quill viewer: gambar + teks --}}
                <div class="media-split__text">
                    <div id="{{ $skelId }}-img" class="space-y-1.5 mb-1">
                        <div class="ql-skeleton-bar w-full"></div>
                        <div class="ql-skeleton-bar w-5/6"></div>
                        <div class="ql-skeleton-bar w-4/6"></div>
                    </div>
                    <div id="{{ $bodyId }}-img" class="ql-body-viewer hidden"></div>
                    <script type="application/json" id="body-data-{{ $content->id }}-img">{!! json_encode($content->body) !!}</script>
                </div>
            </div>

            @elseif($hasImage && !$hasBody)
            <div class="mb-4 space-y-2">
                @foreach($images as $img)
                <div class="media-img-only">
                    <img src="{{ $img->getDisplayUrl() }}" alt="{{ $img->title ?? $content->title }}" loading="lazy">
                </div>
                @if($img->description)
                <p class="text-[10px] text-slate-400 text-center leading-tight">{{ $img->description }}</p>
                @endif
                @endforeach
            </div>

            @elseif(!$hasImage && $hasBody && !$content->isUrl() && !$content->isFile())
            {{-- Quill viewer: teks saja --}}
            <div class="mb-4">
                <div id="{{ $skelId }}" class="space-y-1.5 mb-1">
                    <div class="ql-skeleton-bar w-full"></div>
                    <div class="ql-skeleton-bar w-5/6"></div>
                    <div class="ql-skeleton-bar w-4/6"></div>
                </div>
                <div id="{{ $bodyId }}" class="ql-body-viewer hidden"></div>
                <script type="application/json" id="body-data-{{ $content->id }}">{{ json_encode($content->body) }}</script>
            </div>
            @endif

            {{-- URL --}}
            @if($content->isUrl())
            <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer"
               class="mb-3 flex items-center gap-3 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-indigo-700">Buka Tautan</p>
                    <p class="truncate text-[10px] text-indigo-400">{{ $content->url }}</p>
                </div>
            </a>
            @if($hasBody)
            {{-- Quill viewer: deskripsi URL --}}
            <div class="mb-3">
                <div id="{{ $skelId }}-url" class="space-y-1 mb-1">
                    <div class="ql-skeleton-bar w-full"></div>
                    <div class="ql-skeleton-bar w-4/6"></div>
                </div>
                <div id="{{ $bodyId }}-url" class="ql-body-viewer hidden"></div>
                <script type="application/json" id="body-data-{{ $content->id }}-url">{{ json_encode($content->body) }}</script>
            </div>
            @endif
            @endif

            {{-- File --}}
            @if($content->isFile())
            <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer"
               class="mb-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white shadow-sm">
                    <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Unduh File</p>
                    <p class="text-[10px] text-slate-400">Tap untuk membuka</p>
                </div>
            </a>
            @if($hasBody)
            {{-- Quill viewer: deskripsi file --}}
            <div class="mb-3">
                <div id="{{ $skelId }}-file" class="space-y-1 mb-1">
                    <div class="ql-skeleton-bar w-full"></div>
                    <div class="ql-skeleton-bar w-4/6"></div>
                </div>
                <div id="{{ $bodyId }}-file" class="ql-body-viewer hidden"></div>
                <script type="application/json" id="body-data-{{ $content->id }}-file">{{ json_encode($content->body) }}</script>
            </div>
            @endif
            @endif

            {{-- Audio --}}
            @if($audios->isNotEmpty())
            <div class="mt-3 space-y-3">
                @foreach($audios as $audio)
                @php $audioSrc = $audio->getDisplayUrl(); @endphp
                @if($audioSrc)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    @if($audio->title)
                    <p class="mb-2 text-xs font-semibold text-slate-600">🎵 {{ $audio->title }}</p>
                    @endif
                    <audio controls class="w-full" style="height:40px; border-radius:8px">
                        <source src="{{ $audioSrc }}">
                        Browser tidak mendukung audio.
                    </audio>
                    @if($audio->description)
                    <p class="mt-1 text-[10px] text-slate-400">{{ $audio->description }}</p>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
            @endif

        </div>{{-- /slide --}}
        @endforeach

        {{-- QUIZ SLIDE --}}
        @if($hasQuiz)
        <div class="slide-panel px-4 py-5" style="width:{{ round(100/$totalSlides,4) }}%; flex-shrink:0">
            <div class="flex flex-col items-center text-center pt-8 pb-4">
                <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full"
                     style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                    <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-extrabold text-slate-800">Selesai Membaca! 🎉</h3>
                <p class="mt-2 max-w-xs text-xs text-slate-500 leading-relaxed">
                    Kamu sudah menyelesaikan semua materi. Sekarang saatnya uji pemahamanmu.
                </p>
                <div class="mt-5 flex gap-4">
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-5 py-3 text-center">
                        <p class="text-xl font-extrabold text-indigo-600">{{ $contents->count() }}</p>
                        <p class="text-[10px] text-slate-400">Konten</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-5 py-3 text-center">
                        <p class="text-xl font-extrabold text-indigo-600">{{ $quizzes->count() }}</p>
                        <p class="text-[10px] text-slate-400">Soal</p>
                    </div>
                </div>
                <a href="{{ route('user.quizzes.index', $section) }}"
                   class="mt-6 w-full flex items-center justify-center gap-2 rounded-2xl py-4 text-sm font-bold text-white shadow-lg"
                   style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mulai Quiz
                </a>
                <a href="{{ route('user.schemas.show', $learningSchema) }}"
                   class="mt-3 text-xs text-slate-400 underline">Kembali ke Materi</a>
            </div>
        </div>
        @endif

        {{-- EMPTY --}}
        @if($contents->isEmpty() && !$hasQuiz)
        <div class="slide-panel px-4 py-16 text-center" style="width:100%; flex-shrink:0">
            <p class="text-sm text-slate-400">Belum ada konten di section ini.</p>
            <a href="{{ route('user.schemas.show', $learningSchema) }}" class="mt-4 inline-block text-xs text-indigo-500 underline">Kembali</a>
        </div>
        @endif

    </div>{{-- /slides-track --}}
</div>

{{-- BOTTOM NAV --}}
<div class="fixed bottom-0 left-0 right-0 z-50 border-t border-slate-100 bg-white px-4 py-3"
     style="padding-bottom:max(12px, env(safe-area-inset-bottom))">
    <div class="flex items-center gap-3">
        <button id="btn-prev"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-slate-200 text-slate-400 transition active:bg-slate-50"
                style="opacity:0.3; pointer-events:none">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div id="dots" class="flex flex-1 items-center justify-center gap-1.5 overflow-hidden">
            @for($d = 0; $d < $totalSlides; $d++)
            <div class="dot rounded-full transition-all duration-300
                        {{ $d===0 ? 'h-2 w-5 bg-indigo-500' : 'h-2 w-2 bg-slate-200' }}"></div>
            @endfor
        </div>

        <button id="btn-next"
                class="flex h-11 items-center justify-center gap-1.5 rounded-full px-5 text-sm font-semibold text-white transition active:opacity-80"
                style="background:linear-gradient(135deg,#6366f1,#8b5cf6); min-width:90px">
            Lanjut
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>

</div>

{{-- Quill UMD (satu kali load, sama seperti admin) --}}
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
(function () {
    // Inisialisasi semua Quill viewer yang ada di halaman ini
    document.querySelectorAll('.ql-body-viewer').forEach(function (viewer) {
        var id      = viewer.id;                         // e.g. "ql-viewer-12"
        var suffix  = id.replace('ql-viewer-', '');      // e.g. "12" atau "12-img"
        var skelId  = 'ql-skel-' + suffix;
        var dataId  = 'body-data-' + suffix;

        var dataEl = document.getElementById(dataId);
        if (!dataEl) return;

        var htmlBody = JSON.parse(dataEl.textContent || 'null');
        if (!htmlBody) return;

        var quill = new Quill(viewer, {
            theme    : 'snow',
            readOnly : true,
            modules  : { toolbar: false },
        });

        var delta = quill.clipboard.convert({ html: htmlBody });
        quill.setContents(delta, 'silent');

        // Sembunyikan skeleton, tampilkan viewer
        var skel = document.getElementById(skelId);
        if (skel) skel.style.display = 'none';
        viewer.classList.remove('hidden');
    });
}());
</script>

<script>
(function () {
    const total       = {{ $totalSlides }};
    const hasQuiz     = {{ $hasQuiz ? 'true' : 'false' }};
    const contentIds  = {!! $contentIdsJs !!};
    const progressUrl = '{{ route('user.sections.progress.update', $section) }}';
    const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const backUrl     = '{{ route('user.schemas.show', $learningSchema) }}';

    const track   = document.getElementById('slides-track');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const counter = document.getElementById('slide-current');
    const bar     = document.getElementById('progress-bar');
    const dots    = document.querySelectorAll('.dot');

    const readSlides = new Set();
    let cur = 0;

    function saveProgress(slideIndex, forceComplete = false) {
        readSlides.add(slideIndex);
        const readContentIds = Array.from(readSlides)
            .filter(i => i < contentIds.length)
            .map(i => contentIds[i]);

        fetch(progressUrl, {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({
                slide_index  : Math.max(...readSlides),
                total_slides : total,
                content_ids  : readContentIds,
                completed    : forceComplete,
            }),
        }).catch(() => {});
    }

    function setPrev(on) {
        btnPrev.style.opacity       = on ? '1'    : '0.3';
        btnPrev.style.pointerEvents = on ? 'auto' : 'none';
        btnPrev.style.cursor        = on ? 'pointer' : 'default';
    }

    function goTo(n) {
        if (n < 0 || n >= total) return;
        cur = n;
        track.style.transform = `translateX(-${(100 / total) * cur}%)`;
        counter.textContent   = cur + 1;
        bar.style.width       = ((cur + 1) / total * 100) + '%';

        dots.forEach((d, i) => {
            d.style.width      = i === cur ? '20px' : '8px';
            d.style.background = i === cur ? '#6366f1' : '#e2e8f0';
        });

        setPrev(cur > 0);

        const isLast = cur === total - 1;

        if (isLast && hasQuiz) {
            btnNext.style.display = 'none';
        } else if (isLast && !hasQuiz) {
            btnNext.style.display = '';
            btnNext.innerHTML = 'Selesai&nbsp;<svg style="display:inline;vertical-align:middle" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
            btnNext.style.background = 'linear-gradient(135deg,#10b981,#059669)';
        } else {
            btnNext.style.display = '';
            btnNext.innerHTML = 'Lanjut&nbsp;<svg style="display:inline;vertical-align:middle" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>';
            btnNext.style.background = 'linear-gradient(135deg,#6366f1,#8b5cf6)';
        }

        saveProgress(cur, false);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    btnPrev.addEventListener('click', () => goTo(cur - 1));
    btnNext.addEventListener('click', () => {
        if (cur === total - 1 && !hasQuiz) {
            saveProgress(cur, true);
            setTimeout(() => { window.location.href = backUrl; }, 300);
        } else {
            goTo(cur + 1);
        }
    });

    let sx = 0;
    const area = track.parentElement;
    area.addEventListener('touchstart', e => { sx = e.touches[0].clientX; }, { passive: true });
    area.addEventListener('touchend', e => {
        const diff = sx - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) diff > 0 ? goTo(cur + 1) : goTo(cur - 1);
    });

    goTo(0);
}());
</script>

</x-reader-layout>
