<x-app-layout title="Course">
<div class="pb-8">

    @php $pct = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0; @endphp

    {{-- HERO PROGRESS BANNER --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-blue-500 px-5 pt-6 pb-8">
        {{-- decorative circle --}}
        <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -right-2 top-10 h-24 w-24 rounded-full bg-white/5"></div>

        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-200 mb-1">Progress Belajar</p>
        <div class="flex items-end justify-between mb-4">
            <div>
                <p class="text-4xl font-black text-white leading-none">{{ $pct }}<span class="text-xl font-bold text-indigo-200">%</span></p>
                <p class="text-xs text-indigo-200 mt-1">{{ $completedCount }} dari {{ $totalCount }} section selesai</p>
            </div>
            @if($pct === 100)
            <div class="flex items-center gap-1.5 rounded-full bg-emerald-400/20 border border-emerald-300/30 px-3 py-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-xs font-bold text-emerald-300">Selesai!</span>
            </div>
            @endif
        </div>

        {{-- Progress bar --}}
        <div class="relative h-2.5 w-full rounded-full bg-white/20">
            <div class="absolute left-0 top-0 h-2.5 rounded-full bg-white shadow transition-all duration-700" style="width:{{ $pct }}%"></div>
        </div>

        {{-- Stats row --}}
        <div class="mt-4 grid grid-cols-3 gap-2">
            @php
                $inProgressCount = 0;
                $notStartedCount = 0;
                foreach($sectionsMap->flatten() as $s) {
                    $st = $progressMap[$s->id]?->status ?? 'not_started';
                    if ($st === 'in_progress') $inProgressCount++;
                    elseif ($st === 'not_started') $notStartedCount++;
                }
            @endphp
            <div class="rounded-xl bg-white/10 px-3 py-2 text-center">
                <p class="text-base font-black text-white">{{ $completedCount }}</p>
                <p class="text-xs text-indigo-200 leading-tight">Selesai</p>
            </div>
            <div class="rounded-xl bg-white/10 px-3 py-2 text-center">
                <p class="text-base font-black text-white">{{ $inProgressCount }}</p>
                <p class="text-xs text-indigo-200 leading-tight">On Going</p>
            </div>
            <div class="rounded-xl bg-white/10 px-3 py-2 text-center">
                <p class="text-base font-black text-white">{{ $notStartedCount }}</p>
                <p class="text-xs text-indigo-200 leading-tight">Belum Mulai</p>
            </div>
        </div>
    </div>

    {{-- COURSE LIST --}}
    <div class="px-4 pt-5 space-y-6">
        @if($courseTypes->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-600">Belum ada materi tersedia</p>
            <p class="text-xs text-slate-400 mt-1">Materi akan muncul setelah admin mempublikasikannya.</p>
        </div>
        @else
        @foreach($courseTypes as $ct)
        @php
            $ctSections = $sectionsMap->get($ct->id, collect());
            $ctTotal    = $ctSections->count();
            $ctDone     = $ctSections->filter(fn($s) => ($progressMap[$s->id]?->status ?? '') === 'completed')->count();
            $ctPct      = $ctTotal > 0 ? round(($ctDone / $ctTotal) * 100) : 0;
            $allDone    = $ctPct === 100;
        @endphp

        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">

            {{-- Spesialisasi Header --}}
            <div class="flex items-center justify-between px-4 py-3
                        {{ $allDone ? 'bg-emerald-50 border-b border-emerald-100' : 'bg-slate-50 border-b border-slate-100' }}">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl
                                {{ $allDone ? 'bg-emerald-500' : 'bg-indigo-600' }}">
                        @if($allDone)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-white h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-bold {{ $allDone ? 'text-emerald-800' : 'text-slate-800' }}">{{ $ct->name }}</h3>
                        <p class="text-xs {{ $allDone ? 'text-emerald-600' : 'text-slate-400' }}">{{ $ctDone }}/{{ $ctTotal }} section</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Mini progress ring --}}
                    <div class="relative h-10 w-10">
                        <svg class="h-10 w-10 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15" fill="none" stroke="#e2e8f0" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15" fill="none"
                                stroke="{{ $allDone ? '#10b981' : '#4f46e5' }}" stroke-width="3"
                                stroke-dasharray="94.2" stroke-dashoffset="{{ 94.2 - ($ctPct / 100 * 94.2) }}"
                                stroke-linecap="round"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-black {{ $allDone ? 'text-emerald-600' : 'text-indigo-600' }}">{{ $ctPct }}%</span>
                    </div>
                </div>
            </div>

            {{-- Sections --}}
            @if($ctSections->isEmpty())
            <div class="px-4 py-5 text-center">
                <p class="text-xs text-slate-400">Belum ada section untuk spesialisasi ini.</p>
            </div>
            @else
            <div class="divide-y divide-slate-100">
                @foreach($ctSections as $idx => $section)
                @php
                    $prog     = $progressMap[$section->id] ?? null;
                    $unlocked = ($prog?->unlocked ?? false) || ($ctSections->first()->id === $section->id);
                    $status   = $prog?->status ?? 'not_started';
                    $attempt  = $attemptsMap[$section->id] ?? null;

                    // Media type icon
                    $mediaIcon = match($section->media_type ?? 'video_upload') {
                        'youtube'      => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'audio_upload' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3',
                        'drive'        => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z',
                        default        => 'M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z',
                    };
                @endphp
                <div class="flex items-center gap-3 px-4 py-3.5 {{ !$unlocked ? 'opacity-50' : '' }}">

                    {{-- Number / status circle --}}
                    <div class="relative shrink-0">
                        @if($status === 'completed')
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        @elseif($status === 'in_progress')
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 border-2 border-amber-400">
                                <span class="text-sm font-black text-amber-600">{{ $idx + 1 }}</span>
                            </div>
                            <span class="absolute -right-0.5 -top-0.5 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                            </span>
                        @elseif(!$unlocked)
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 text-slate-400 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                        @else
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 border-2 border-indigo-200">
                                <span class="text-sm font-black text-indigo-600">{{ $idx + 1 }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            {{-- media type icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $mediaIcon }}"/>
                            </svg>
                            <p class="text-xs text-slate-400">
                                {{ match($section->media_type ?? 'video_upload') {
                                    'youtube'      => 'YouTube',
                                    'audio_upload' => 'Audio',
                                    'drive'        => 'Drive',
                                    default        => 'Video',
                                } }}
                                @if($section->quizzes_count > 0)
                                    &bull; {{ $section->quizzes_count }} Quiz
                                @endif
                            </p>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 leading-tight truncate">{{ $section->title }}</h4>
                        @if($attempt)
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $attempt->total_attempts }}× percobaan
                                @if($attempt->ever_passed)
                                    &bull; <span class="font-semibold text-emerald-600">Lulus ✓</span>
                                @else
                                    &bull; Best {{ $attempt->best_score }}%
                                @endif
                            </p>
                        @endif
                    </div>

                    {{-- Action arrow / lock --}}
                    @if($unlocked)
                        <a href="{{ route('user.section.show', $section) }}"
                           class="shrink-0 flex h-9 w-9 items-center justify-center rounded-full
                                  {{ $status === 'completed' ? 'bg-emerald-100 text-emerald-600' : 'bg-indigo-600 text-white shadow-sm' }}
                                  active:scale-95 transition">
                            @if($status === 'completed')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            @endif
                        </a>
                    @else
                        <div class="shrink-0 flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    @endif

                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
        @endif
    </div>
</div>
</x-app-layout>
