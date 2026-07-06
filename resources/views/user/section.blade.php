{{-- resources/views/user/section.blade.php --}}
<x-reader-layout :title="$section->title">
@php
    $contents    = $section->contents;
    $quizzes     = $section->quizzes;
    $totalSlides = $contents->count() + ($quizzes->isNotEmpty() ? 1 : 0);
    $hasQuiz     = $quizzes->isNotEmpty();
    if ($totalSlides === 0) $totalSlides = 1;

    // Content IDs sebagai array JS
    $contentIdsJs = $contents->pluck('id')->toJson();
@endphp

<style>
.media-split {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.media-split__img {
    flex: 0 0 25%;
    max-width: 25%;
    min-width: 0;
}
.media-split__img-wrap {
    width: 100%;
    aspect-ratio: 1 / 1;
    border-radius: 12px;
    overflow: hidden;
    background: #f1f5f9;
}
.media-split__img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.media-split__text {
    flex: 1 1 0;
    min-width: 0;
}
.media-img-only {
    width: 100%;
    border-radius: 14px;
    overflow: hidden;
    background: #f1f5f9;
}
.media-img-only img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 14px;
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
            $hasImage = $images->isNotEmpty();
            $hasBody  = !empty(trim(strip_tags($content->body ?? '')));

            $embedVideo = function(string $url): string {
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $url, $m)) {
                    return 'https://www.youtube.com/embed/' . $m[1];
                }
                return $url;
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
                $vEmbed = $vRaw ? $embedVideo($vRaw) : '';
            @endphp
            @if($vEmbed)
            <div class="mb-4 rounded-2xl overflow-hidden bg-slate-900" style="position:relative; padding-top:56.25%">
                <iframe src="{{ $vEmbed }}"
                        class="absolute inset-0 h-full w-full"
                        frameborder="0" allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
            @endif

            {{-- 1/4 gambar | 3/4 teks --}}
            @if($hasImage && $hasBody)
            <div class="mb-4 media-split">
                <div class="media-split__img">
                    @foreach($images as $img)
                    <div class="media-split__img-wrap" style="{{ !$loop->first ? 'margin-top:8px' : '' }}">
                        <img src="{{ $img->getDisplayUrl() }}"
                             alt="{{ $img->title ?? $content->title }}"
                             loading="lazy">
                    </div>
                    @if($img->description)
                    <p class="mt-1 text-[10px] text-slate-400 text-center leading-tight">{{ $img->description }}</p>
                    @endif
                    @endforeach
                </div>
                <div class="media-split__text content-body text-sm text-slate-700 leading-relaxed">
                    {!! $content->body !!}
                </div>
            </div>

            @elseif($hasImage && !$hasBody)
            <div class="mb-4 space-y-2">
                @foreach($images as $img)
                <div class="media-img-only">
                    <img src="{{ $img->getDisplayUrl() }}"
                         alt="{{ $img->title ?? $content->title }}"
                         loading="lazy">
                </div>
                @if($img->description)
                <p class="text-[10px] text-slate-400 text-center leading-tight">{{ $img->description }}</p>
                @endif
                @endforeach
            </div>

            @elseif(!$hasImage && $hasBody && !$content->isUrl() && !$content->isFile())
            <div class="mb-4 content-body text-sm text-slate-700 leading-relaxed">
                {!! $content->body !!}
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
            <div class="mb-3 content-body text-xs text-slate-500">{!! $content->body !!}</div>
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
            <div class="mb-3 content-body text-xs text-slate-500">{!! $content->body !!}</div>
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

<script>
(function () {
    const total       = {{ $totalSlides }};
    const contentIds  = {!! $contentIdsJs !!};   // array id konten
    const progressUrl = '{{ route('user.sections.progress.update', $section) }}';
    const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const track   = document.getElementById('slides-track');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const counter = document.getElementById('slide-current');
    const bar     = document.getElementById('progress-bar');
    const dots    = document.querySelectorAll('.dot');

    // Set untuk melacak slide mana saja yang sudah dibaca
    const readSlides = new Set();
    let cur = 0;

    // ── Kirim progres ke server ──────────────────────────────────────
    function saveProgress(slideIndex) {
        readSlides.add(slideIndex);

        // Kumpulkan content_ids yang sudah dibaca (hanya slide konten, bukan quiz)
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
                slide_index  : Math.max(...readSlides),   // slide terjauh yang sudah dilihat
                total_slides : total,
                content_ids  : readContentIds,
            }),
        }).catch(() => {/* silent fail */});
    }

    // ── Update UI ────────────────────────────────────────────────────
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
        if (isLast) {
            btnNext.innerHTML = 'Selesai&nbsp;<svg style="display:inline;vertical-align:middle" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
            btnNext.style.background = 'linear-gradient(135deg,#10b981,#059669)';
        } else {
            btnNext.innerHTML = 'Lanjut&nbsp;<svg style="display:inline;vertical-align:middle" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>';
            btnNext.style.background = 'linear-gradient(135deg,#6366f1,#8b5cf6)';
        }

        // Catat slide ini sebagai sudah dibaca & kirim ke server
        saveProgress(cur);

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    btnPrev.addEventListener('click', () => goTo(cur - 1));
    btnNext.addEventListener('click', () => {
        if (cur === total - 1) {
            window.location.href = '{{ route('user.schemas.show', $learningSchema) }}';
        } else {
            goTo(cur + 1);
        }
    });

    // Swipe
    let sx = 0;
    const area = track.parentElement;
    area.addEventListener('touchstart', e => { sx = e.touches[0].clientX; }, { passive: true });
    area.addEventListener('touchend', e => {
        const diff = sx - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) diff > 0 ? goTo(cur + 1) : goTo(cur - 1);
    });

    // Inisiasi: catat slide pertama
    goTo(0);
}());
</script>

</x-reader-layout>
