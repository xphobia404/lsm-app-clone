<x-app-layout :title="$section->title">
<div class="pb-28">

    {{-- Flash --}}
    @if(session('success'))
        <div class="px-4 pt-4"><x-alert type="success">{{ session('success') }}</x-alert></div>
    @endif
    @if(session('info'))
        <div class="px-4 pt-4"><x-alert type="info">{{ session('info') }}</x-alert></div>
    @endif

    {{-- ════════════════════════ MEDIA AREA ════════════════════════ --}}
    @php
        $mediaType = $section->media_type ?? 'video_upload';
        $mediaUrl  = $section->media_play_url ?? null;
        $isMulti   = $section->isMultiPage();
        $pages     = $section->parsed_pages; // always array
        $totalPages = count($pages);
    @endphp

    @if($mediaType === 'youtube' && $mediaUrl)
    <div class="w-full bg-black">
        @php preg_match('/(?:v=|\/embed\/|youtu\.be\/)([\w-]{11})/', $mediaUrl, $m); $videoId = $m[1] ?? ''; @endphp
        @if($videoId)
        <div class="aspect-video w-full">
            <iframe class="h-full w-full" src="https://www.youtube.com/embed/{{ $videoId }}?rel=0"
                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        @endif
    </div>
    @elseif($mediaType === 'drive' && $mediaUrl)
    <div class="w-full bg-black">
        @php preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $mediaUrl, $dm); $driveId = $dm[1] ?? ''; @endphp
        <div class="aspect-video w-full">
            @if($driveId)
            <iframe class="h-full w-full" src="https://drive.google.com/file/d/{{ $driveId }}/preview"
                frameborder="0" allow="autoplay" allowfullscreen></iframe>
            @else
            <div class="flex h-full items-center justify-center bg-slate-900">
                <a href="{{ $mediaUrl }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-2 rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white">Buka di Drive</a>
            </div>
            @endif
        </div>
    </div>
    @elseif($mediaType === 'video_upload' && $mediaUrl)
    <div class="w-full bg-black">
        <video class="w-full" controls controlslist="nodownload" preload="metadata"
               @if($section->thumbnail_url) poster="{{ $section->thumbnail_url }}" @endif>
            <source src="{{ $mediaUrl }}" type="video/mp4">
        </video>
    </div>
    @elseif($mediaType === 'audio_upload' && $mediaUrl)
    <div class="w-full bg-gradient-to-br from-slate-800 to-slate-900 px-5 py-6">
        <div class="flex items-center gap-4 mb-4">
            @if($section->thumbnail_url)
                <img src="{{ $section->thumbnail_url }}" alt="" class="h-16 w-16 rounded-2xl object-cover" loading="lazy">
            @else
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-0.5">Audio Materi</p>
                <p class="text-sm font-bold text-white truncate">{{ $section->title }}</p>
            </div>
        </div>
        <audio class="w-full" controls controlslist="nodownload" preload="metadata">
            <source src="{{ $mediaUrl }}" type="audio/mpeg">
            <source src="{{ $mediaUrl }}" type="audio/ogg">
        </audio>
    </div>
    @elseif(!$isMulti && $section->thumbnail_url)
    <img src="{{ $section->thumbnail_url }}" alt="{{ $section->title }}" class="w-full object-cover max-h-52" loading="lazy">
    @endif

    {{-- ════════════════════════ CONTENT AREA ════════════════════════ --}}
    <div class="px-4 pt-4">

        {{-- Section Header --}}
        <div class="mb-4">
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full">Section {{ $section->order }}</span>
                @if($isMulti)
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full" id="page-badge">
                        📄 Page 1 / {{ $totalPages }}
                    </span>
                @endif
                <x-badge-status :status="$progress->status" />
            </div>
            <h1 class="text-lg font-bold text-slate-900">{{ $section->title }}</h1>
            @if($section->description)
                <p class="mt-1 text-sm text-slate-500">{{ $section->description }}</p>
            @endif
        </div>

        {{-- ──────────── SINGLE PAGE ──────────── --}}
        @if(!$isMulti)
            @if($section->content)
            <div class="prose prose-sm max-w-none text-slate-700
                        prose-headings:font-bold prose-headings:text-slate-900
                        prose-h2:text-base prose-h3:text-sm prose-p:leading-relaxed
                        prose-ul:list-disc prose-ul:pl-5 prose-ol:list-decimal prose-ol:pl-5
                        prose-li:text-slate-700 prose-strong:text-slate-900
                        prose-blockquote:border-l-4 prose-blockquote:border-blue-300 prose-blockquote:pl-4 prose-blockquote:text-slate-500
                        prose-code:bg-slate-100 prose-code:rounded prose-code:px-1 prose-code:text-xs">
                {!! $section->content !!}
            </div>
            @endif

        {{-- ──────────── MULTI PAGE VIEWER ──────────── --}}
        @else
        <div id="multipage-viewer">
            {{-- Top progress bar --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-slate-400" id="mp-counter">Page 1 dari {{ $totalPages }}</span>
                    <span class="text-xs font-semibold text-indigo-600" id="mp-pct">{{ $totalPages > 0 ? round(1/$totalPages*100) : 100 }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-slate-200">
                    <div id="mp-progress" class="h-1.5 rounded-full bg-indigo-500 transition-all duration-300"
                         style="width:{{ $totalPages > 0 ? round(1/$totalPages*100) : 100 }}%"></div>
                </div>
            </div>

            {{-- Pages --}}
            @foreach($pages as $pi => $page)
            <div id="mp-page-{{ $pi }}" class="mp-page {{ $pi !== 0 ? 'hidden' : '' }}">

                {{-- Page title --}}
                @if(!empty($page['title']))
                    <h2 class="mb-3 text-base font-bold text-slate-900">{{ $page['title'] }}</h2>
                @endif

                {{-- Page image --}}
                @if(!empty($page['image_url']))
                    <img src="{{ $page['image_url'] }}" alt="" loading="lazy"
                         class="mb-4 w-full rounded-2xl object-cover max-h-64 border border-slate-100">
                @endif

                {{-- ★ Per-slide audio player --}}
                @if(!empty($page['audio_url']))
                <div class="mp-slide-audio mb-4 rounded-2xl bg-gradient-to-r from-indigo-50 to-violet-50 border border-indigo-100 p-3">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-indigo-700">🎵 Audio Slide {{ $pi + 1 }}</p>
                            @if(!empty($page['title']))
                                <p class="text-xs text-indigo-400 truncate">{{ $page['title'] }}</p>
                            @endif
                        </div>
                    </div>
                    <audio id="mp-audio-{{ $pi }}" class="w-full" controls controlslist="nodownload" preload="none">
                        <source src="{{ $page['audio_url'] }}" type="audio/mpeg">
                        <source src="{{ $page['audio_url'] }}" type="audio/ogg">
                        <source src="{{ $page['audio_url'] }}" type="audio/wav">
                        Browser Anda tidak mendukung pemutar audio.
                    </audio>
                </div>
                @endif

                {{-- Page content --}}
                @if(!empty($page['content']))
                <div class="prose prose-sm max-w-none text-slate-700
                            prose-headings:font-bold prose-headings:text-slate-900
                            prose-h2:text-base prose-h3:text-sm prose-p:leading-relaxed
                            prose-ul:list-disc prose-ul:pl-5 prose-ol:list-decimal prose-ol:pl-5
                            prose-li:text-slate-700 prose-strong:text-slate-900
                            prose-blockquote:border-l-4 prose-blockquote:border-blue-300 prose-blockquote:pl-4 prose-blockquote:text-slate-500
                            prose-code:bg-slate-100 prose-code:rounded prose-code:px-1 prose-code:text-xs">
                    {!! $page['content'] !!}
                </div>
                @endif
            </div>
            @endforeach

            {{-- Page navigation (inline, inside content area) --}}
            <div class="mt-6 flex items-center gap-3" id="mp-nav">
                <button id="mp-btn-prev"
                        onclick="mpGo(mpCurrent - 1)"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 active:bg-slate-50 transition disabled:opacity-30 shadow-sm"
                        disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Sebelumnya
                </button>
                <button id="mp-btn-next"
                        onclick="mpGo(mpCurrent + 1)"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm active:scale-[0.98] transition disabled:opacity-30"
                        {{ $totalPages <= 1 ? 'disabled' : '' }}>
                    Selanjutnya
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            {{-- Dot indicators --}}
            @if($totalPages > 1)
            <div class="mt-3 flex items-center justify-center gap-1.5">
                @foreach($pages as $pi => $page)
                    <button onclick="mpGo({{ $pi }})" id="mp-dot-{{ $pi }}"
                            class="rounded-full transition-all duration-200 {{ $pi === 0 ? 'w-4 h-2 bg-indigo-600' : 'w-2 h-2 bg-slate-300' }}"
                            aria-label="Page {{ $pi + 1 }}"></button>
                @endforeach
            </div>
            @endif
        </div>

        <script>
        (function(){
            const total = {{ $totalPages }};
            window.mpCurrent = 0;

            // Pause audio on the given slide index
            function pauseSlideAudio(idx) {
                const audio = document.getElementById('mp-audio-' + idx);
                if (audio && !audio.paused) {
                    audio.pause();
                }
            }

            window.mpGo = function(n) {
                if (n < 0 || n >= total) return;

                // Pause audio of current slide before leaving
                pauseSlideAudio(mpCurrent);

                // Hide current
                document.getElementById('mp-page-' + mpCurrent)?.classList.add('hidden');
                document.getElementById('mp-dot-' + mpCurrent)?.classList.replace('w-4','w-2');
                document.getElementById('mp-dot-' + mpCurrent)?.classList.replace('bg-indigo-600','bg-slate-300');

                // Show next
                mpCurrent = n;
                document.getElementById('mp-page-' + mpCurrent)?.classList.remove('hidden');
                document.getElementById('mp-dot-' + mpCurrent)?.classList.replace('w-2','w-4');
                document.getElementById('mp-dot-' + mpCurrent)?.classList.replace('bg-slate-300','bg-indigo-600');

                // Update progress
                const pct = Math.round((mpCurrent + 1) / total * 100);
                document.getElementById('mp-progress').style.width = pct + '%';
                document.getElementById('mp-counter').textContent = 'Page ' + (mpCurrent + 1) + ' dari ' + total;
                document.getElementById('mp-pct').textContent = pct + '%';
                document.getElementById('page-badge').textContent = '\u{1F4C4} Page ' + (mpCurrent + 1) + ' / ' + total;

                // Buttons
                document.getElementById('mp-btn-prev').disabled = mpCurrent === 0;
                document.getElementById('mp-btn-next').disabled = mpCurrent === total - 1;

                // Preload audio of new slide so it's ready to play
                const newAudio = document.getElementById('mp-audio-' + mpCurrent);
                if (newAudio && newAudio.preload === 'none') {
                    newAudio.preload = 'metadata';
                    newAudio.load();
                }

                // Scroll to top of content
                document.getElementById('multipage-viewer')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };

            // Preload first slide audio on page load
            const firstAudio = document.getElementById('mp-audio-0');
            if (firstAudio) {
                firstAudio.preload = 'metadata';
                firstAudio.load();
            }
        })();
        </script>
        @endif

        {{-- Quiz CTA --}}
        @if($hasQuiz)
        <div class="mt-6 rounded-2xl border p-4 {{ $quizPassed ? 'border-emerald-200 bg-emerald-50' : 'border-indigo-200 bg-indigo-50' }}">
            @if($quizPassed)
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-800">Quiz Sudah Lulus! 🎉</p>
                        <p class="text-xs text-emerald-600 mt-0.5">{{ $nextSection ? 'Section berikutnya sudah terbuka.' : 'Kamu telah menyelesaikan semua materi!' }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-start gap-3 mb-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-indigo-800">Quiz Section Ini</p>
                        <p class="text-xs text-indigo-600 mt-0.5">Jawab semua soal untuk membuka section berikutnya.</p>
                    </div>
                </div>
                <a href="{{ route('user.quiz.show', $section) }}"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm active:scale-[0.98] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Kerjakan Quiz
                </a>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- Bottom Navigation (prev/next section) --}}
<div class="fixed bottom-0 left-0 right-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur-sm px-4 py-3">
    <div class="flex items-center justify-between gap-3">
        @if($prevSection)
            <a href="{{ route('user.section.show', $prevSection) }}"
               class="flex flex-1 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Sebelumnya
            </a>
        @else
            <a href="{{ route('user.courses') }}"
               class="flex flex-1 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 active:bg-slate-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Semua Materi
            </a>
        @endif

        @if($nextSection)
            @if($nextUnlocked)
                <a href="{{ route('user.section.show', $nextSection) }}"
                   class="flex flex-1 items-center justify-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm active:scale-[0.98] transition">
                    Section Berikutnya
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <div class="flex flex-1 items-center justify-center gap-1.5 rounded-full bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Terkunci
                </div>
            @endif
        @else
            <a href="{{ route('user.courses') }}"
               class="flex flex-1 items-center justify-center gap-1.5 rounded-full bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm active:scale-[0.98] transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Selesai!
            </a>
        @endif
    </div>
</div>
</x-app-layout>
