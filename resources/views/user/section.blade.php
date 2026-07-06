{{-- resources/views/user/section.blade.php --}}
<x-app-layout :title="$section->title">
@php
    $contents    = $section->contents;   // sudah active+ordered
    $quizzes     = $section->quizzes;
    $totalSlides = $contents->count() + ($quizzes->isNotEmpty() ? 1 : 0); // +1 slide quiz
    $hasQuiz     = $quizzes->isNotEmpty();
@endphp

{{-- ================================================================
     TOP BAR
================================================================ --}}
<div class="sticky top-0 z-30 flex items-center gap-3 border-b border-slate-100 bg-white/95 px-4 py-3"
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

    {{-- progress pill --}}
    <div class="shrink-0 flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1">
        <span id="slide-current" class="text-[11px] font-extrabold text-indigo-600">1</span>
        <span class="text-[10px] text-indigo-300">/</span>
        <span class="text-[11px] font-bold text-indigo-400">{{ $totalSlides }}</span>
    </div>
</div>

{{-- Progress bar thin --}}
<div class="h-0.5 w-full bg-slate-100">
    <div id="progress-bar" class="h-0.5 bg-indigo-500 transition-all duration-300"
         style="width: {{ $totalSlides > 0 ? round(1/$totalSlides*100) : 100 }}%"></div>
</div>

{{-- ================================================================
     SLIDER WRAPPER
================================================================ --}}
<div class="overflow-hidden">
    <div id="slides-track"
         class="flex transition-transform duration-300 ease-in-out"
         style="width: {{ $totalSlides * 100 }}%">

        {{-- ── CONTENT SLIDES ────────────────────────────────── --}}
        @foreach($contents as $i => $content)
        <div class="slide-panel flex-shrink-0 px-4 py-5"
             style="width: {{ $totalSlides > 0 ? round(100/$totalSlides, 4) : 100 }}%">

            {{-- Content header --}}
            <div class="mb-4 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-extrabold text-indigo-600">
                    {{ $i + 1 }}
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ $content->title }}</p>
                    <p class="text-[10px] text-slate-400 capitalize">{{ $content->content_type }}</p>
                </div>
            </div>

            {{-- Content body --}}
            @if($content->isText())
                <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                    {!! nl2br(e($content->body)) !!}
                </div>

            @elseif($content->isVideo())
                @php $url = $content->url ?? $content->body; @endphp
                @php
                    // Convert YouTube watch URL ke embed
                    $embedUrl = $url;
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $url, $m)) {
                        $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
                    }
                @endphp
                <div class="relative rounded-2xl overflow-hidden bg-black" style="padding-top:56.25%">
                    <iframe src="{{ $embedUrl }}"
                            class="absolute inset-0 h-full w-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
                @if($content->body && $content->body !== $url)
                <p class="mt-3 text-xs text-slate-500">{{ $content->body }}</p>
                @endif

            @elseif($content->isUrl())
                <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-3 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-4">
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
                @if($content->body)
                <p class="mt-3 text-xs text-slate-500">{{ $content->body }}</p>
                @endif

            @elseif($content->isFile())
                <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
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
                @if($content->body)
                <p class="mt-3 text-xs text-slate-500">{{ $content->body }}</p>
                @endif
            @endif
        </div>
        @endforeach

        {{-- ── QUIZ SLIDE (slide terakhir) ───────────────────── --}}
        @if($hasQuiz)
        <div class="slide-panel flex-shrink-0 px-4 py-5"
             style="width: {{ $totalSlides > 0 ? round(100/$totalSlides, 4) : 100 }}%">

            <div class="flex flex-col items-center text-center pt-6 pb-4">
                {{-- Ikon --}}
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full"
                     style="background: linear-gradient(135deg,#6366f1,#8b5cf6)">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                                 m-6 9l2 2 4-4"/>
                    </svg>
                </div>

                <h3 class="text-base font-extrabold text-slate-800">Selesai Membaca!</h3>
                <p class="mt-1.5 max-w-xs text-xs text-slate-500 leading-relaxed">
                    Kamu sudah menyelesaikan semua materi di section ini.
                    Sekarang saatnya uji pemahamanmu.
                </p>

                {{-- Stats --}}
                <div class="mt-5 flex gap-4">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-center">
                        <p class="text-lg font-extrabold text-indigo-600">{{ $contents->count() }}</p>
                        <p class="text-[10px] text-slate-400">Konten</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-center">
                        <p class="text-lg font-extrabold text-indigo-600">{{ $quizzes->count() }}</p>
                        <p class="text-[10px] text-slate-400">Soal Quiz</p>
                    </div>
                </div>

                {{-- CTA --}}
                <a href="{{ route('user.quizzes.index', $section) }}"
                   class="mt-6 w-full flex items-center justify-center gap-2 rounded-2xl py-4 text-sm font-bold text-white shadow-lg"
                   style="background: linear-gradient(135deg,#6366f1,#8b5cf6)">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mulai Quiz
                </a>
            </div>
        </div>
        @endif

        {{-- ── SLIDE SELESAI (jika tidak ada quiz) ─────────── --}}
        @if(!$hasQuiz && $contents->isEmpty())
        <div class="slide-panel flex-shrink-0 px-4 py-10 text-center"
             style="width:100%">
            <p class="text-sm font-medium text-slate-400">Belum ada konten di section ini.</p>
        </div>
        @endif

    </div>{{-- /slides-track --}}
</div>

{{-- ================================================================
     BOTTOM NAVIGATION
================================================================ --}}
<div class="fixed bottom-0 left-0 right-0 z-30 border-t border-slate-100 bg-white px-4 py-3"
     style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom))">
    <div class="flex items-center gap-3">

        {{-- Prev button --}}
        <button id="btn-prev"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-slate-200 text-slate-400 disabled:opacity-30 transition active:bg-slate-50"
                disabled>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Dot indicators --}}
        <div id="dots" class="flex flex-1 items-center justify-center gap-1.5 overflow-hidden">
            @for($d = 0; $d < $totalSlides; $d++)
            <div class="dot h-2 rounded-full transition-all duration-300
                        {{ $d === 0 ? 'w-5 bg-indigo-500' : 'w-2 bg-slate-200' }}"></div>
            @endfor
        </div>

        {{-- Next button --}}
        <button id="btn-next"
                class="flex h-11 items-center justify-center gap-1.5 rounded-full px-5
                       text-sm font-semibold text-white transition active:opacity-80"
                style="background: linear-gradient(135deg,#6366f1,#8b5cf6); min-width:90px">
            Lanjut
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

    </div>
</div>

{{-- spacer agar konten tidak tertutup fixed bottom nav --}}
<div class="h-20"></div>

{{-- ================================================================
     JS SLIDER
================================================================ --}}
<script>
(function () {
    const total    = {{ $totalSlides }};
    const track    = document.getElementById('slides-track');
    const btnPrev  = document.getElementById('btn-prev');
    const btnNext  = document.getElementById('btn-next');
    const counter  = document.getElementById('slide-current');
    const progBar  = document.getElementById('progress-bar');
    const dots     = document.querySelectorAll('.dot');
    let current = 0;

    function goTo(index) {
        if (index < 0 || index >= total) return;
        current = index;
        track.style.transform = `translateX(-${(100 / total) * current}%)`;

        // counter
        counter.textContent = current + 1;

        // progress bar
        progBar.style.width = ((current + 1) / total * 100) + '%';

        // dots
        dots.forEach((d, i) => {
            d.classList.toggle('w-5', i === current);
            d.classList.toggle('bg-indigo-500', i === current);
            d.classList.toggle('w-2', i !== current);
            d.classList.toggle('bg-slate-200', i !== current);
        });

        // prev button
        btnPrev.disabled = current === 0;

        // next button: ubah label di slide terakhir
        const isLast = current === total - 1;
        if (isLast) {
            btnNext.innerHTML = `Selesai
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>`;
            btnNext.style.background = 'linear-gradient(135deg,#10b981,#059669)';
        } else {
            btnNext.innerHTML = `Lanjut
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>`;
            btnNext.style.background = 'linear-gradient(135deg,#6366f1,#8b5cf6)';
        }

        // scroll ke atas slide
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    btnPrev.addEventListener('click', () => goTo(current - 1));
    btnNext.addEventListener('click', () => {
        if (current === total - 1) {
            // slide terakhir & tidak ada quiz: kembali ke schema
            @if(!$hasQuiz)
            window.location.href = '{{ route('user.schemas.show', $learningSchema) }}';
            @else
            // slide terakhir adalah slide quiz — tombol Mulai Quiz sudah ada di slide
            // Selesai = kembali ke schema
            window.location.href = '{{ route('user.schemas.show', $learningSchema) }}';
            @endif
        } else {
            goTo(current + 1);
        }
    });

    // swipe support
    let startX = 0;
    const wrapper = document.getElementById('slides-track').parentElement;
    wrapper.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    wrapper.addEventListener('touchend', e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) diff > 0 ? goTo(current + 1) : goTo(current - 1);
    });

    goTo(0);
}());
</script>

</x-app-layout>
