{{-- resources/views/user/dashboard.blade.php --}}
<x-app-layout title="Dashboard">
<div class="pb-12">

    {{-- ============================================================
         HERO GREETING
    ============================================================ --}}
    <div class="relative overflow-hidden px-5 pt-6 pb-8"
         style="background: linear-gradient(135deg, #1e3a8a 0%, #312e81 50%, #4c1d95 100%)">

        {{-- decorative circles --}}
        <div class="absolute -top-6 -right-6 h-32 w-32 rounded-full opacity-10" style="background:#fff"></div>
        <div class="absolute top-10 -right-2 h-16 w-16 rounded-full opacity-10" style="background:#fff"></div>

        <p class="text-xs font-medium text-indigo-300 mb-0.5">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
        </p>
        <h1 class="text-lg font-extrabold text-white leading-snug">
            Halo, {{ auth()->user()->name ?: auth()->user()->username }}! 👋
        </h1>
        <p class="mt-0.5 text-xs text-indigo-200">Terus semangat belajar hari ini</p>

        {{-- Overall progress ring --}}
        <div class="mt-5 flex items-center gap-4 rounded-2xl px-4 py-3.5"
             style="background: rgba(255,255,255,0.12); backdrop-filter: blur(8px)">
            {{-- SVG ring --}}
            <div class="relative flex-shrink-0" style="width:56px;height:56px">
                <svg width="56" height="56" viewBox="0 0 56 56">
                    <circle cx="28" cy="28" r="22" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="5"/>
                    <circle cx="28" cy="28" r="22" fill="none" stroke="#a5f3fc" stroke-width="5"
                            stroke-linecap="round"
                            stroke-dasharray="{{ round(2 * 3.14159 * 22) }}"
                            stroke-dashoffset="{{ round(2 * 3.14159 * 22 * (1 - $overallPct / 100)) }}"
                            transform="rotate(-90 28 28)"
                            style="transition: stroke-dashoffset 1s ease"/>
                </svg>
                <span class="absolute inset-0 flex items-center justify-center text-xs font-extrabold text-white">
                    {{ $overallPct }}%
                </span>
            </div>
            <div>
                <p class="text-sm font-bold text-white">Progress Keseluruhan</p>
                <p class="text-xs text-indigo-200 mt-0.5">
                    {{ $completedCount }} selesai &bull; {{ $inProgressCount }} sedang berjalan
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STATS ROW
    ============================================================ --}}
    <div class="px-4 -mt-1">
        <div class="grid grid-cols-3 gap-2.5 mt-4">
            {{-- Selesai --}}
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-3 text-center">
                <div class="mx-auto mb-1.5 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-50">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-xl font-extrabold text-emerald-600">{{ $completedCount }}</p>
                <p class="text-[10px] text-slate-400 leading-tight">Section<br>Selesai</p>
            </div>

            {{-- In Progress --}}
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-3 text-center">
                <div class="mx-auto mb-1.5 flex h-8 w-8 items-center justify-center rounded-full bg-amber-50">
                    <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xl font-extrabold text-amber-500">{{ $inProgressCount }}</p>
                <p class="text-[10px] text-slate-400 leading-tight">Sedang<br>Berjalan</p>
            </div>

            {{-- Quiz --}}
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-3 text-center">
                <div class="mx-auto mb-1.5 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50">
                    <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-xl font-extrabold text-indigo-600">{{ $avgScore }}%</p>
                <p class="text-[10px] text-slate-400 leading-tight">Rata Quiz<br>({{ $quizTotal }}x)</p>
            </div>
        </div>
    </div>

    {{-- ============================================================
         LANJUTKAN BELAJAR
    ============================================================ --}}
    @if($continueSections->isNotEmpty())
    <div class="px-4 mt-6">
        <p class="text-sm font-bold text-slate-800 mb-3">&#128218; Lanjutkan Belajar</p>
        <div class="space-y-2">
            @foreach($continueSections as $progress)
            @php
                $sec    = $progress->section;
                $schema = $sec ? $sec->learningSchemas->first() : null;
            @endphp
            @if($sec && $schema)
            <a href="{{ route('user.sections.show', [$schema, $sec]) }}"
               class="flex items-center gap-3 rounded-2xl bg-white border border-amber-100 px-4 py-3 shadow-sm active:bg-amber-50 transition">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100">
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800 truncate">{{ $sec->title }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ $schema->title }}</p>
                </div>
                <span class="shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">Lanjut</span>
            </a>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- ============================================================
         PROGRESS PER MATERI
    ============================================================ --}}
    <div class="px-4 mt-6">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-bold text-slate-800">&#128218; Progress Per Materi</p>
            <a href="{{ route('user.schemas.index') }}"
               class="text-xs font-medium text-indigo-600">Lihat Semua</a>
        </div>

        @forelse($schemaStats as $stat)
        @php
            $pct    = $stat['pct'];
            $schema = $stat['schema'];
            $total  = $stat['total'];
            $done   = $stat['done'];
            // warna berdasar progress
            if ($pct >= 100) {
                $barColor   = '#10b981'; // emerald
                $badgeBg    = 'bg-emerald-50';
                $badgeText  = 'text-emerald-700';
                $badgeLabel = 'Selesai';
            } elseif ($pct > 0) {
                $barColor   = '#f59e0b'; // amber
                $badgeBg    = 'bg-amber-50';
                $badgeText  = 'text-amber-700';
                $badgeLabel = 'Berlangsung';
            } else {
                $barColor   = '#6366f1'; // indigo
                $badgeBg    = 'bg-slate-50';
                $badgeText  = 'text-slate-500';
                $badgeLabel = 'Belum mulai';
            }
        @endphp
        <a href="{{ route('user.schemas.show', $schema) }}"
           class="block mb-3 rounded-2xl bg-white border border-slate-100 px-4 py-4 shadow-sm active:bg-slate-50 transition">

            <div class="flex items-start justify-between gap-2 mb-2">
                <p class="text-xs font-semibold text-slate-800 leading-snug flex-1">{{ $schema->title }}</p>
                <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $badgeBg }} {{ $badgeText }}">
                    {{ $badgeLabel }}
                </span>
            </div>

            {{-- Progress bar --}}
            <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                <div class="h-1.5 rounded-full transition-all"
                     style="width: {{ $pct }}%; background: {{ $barColor }}"></div>
            </div>

            <div class="flex items-center justify-between mt-1.5">
                <p class="text-[10px] text-slate-400">{{ $done }}/{{ $total }} section selesai</p>
                <p class="text-[10px] font-bold" style="color: {{ $barColor }}">{{ $pct }}%</p>
            </div>
        </a>
        @empty
        <div class="rounded-2xl bg-slate-50 p-10 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-3 h-10 w-10 text-slate-300"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-sm font-medium text-slate-500">Belum ada materi tersedia</p>
            <p class="mt-1 text-xs text-slate-400">Materi akan ditambahkan segera</p>
        </div>
        @endforelse
    </div>

    {{-- ============================================================
         RIWAYAT QUIZ
    ============================================================ --}}
    @if($quizTotal > 0)
    <div class="px-4 mt-2 mb-4">
        <p class="text-sm font-bold text-slate-800 mb-3">&#127942; Statistik Quiz</p>
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
            <div class="grid grid-cols-3 divide-x divide-slate-100">
                <div class="px-3 py-4 text-center">
                    <p class="text-xl font-extrabold text-slate-800">{{ $quizTotal }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Total<br>Percobaan</p>
                </div>
                <div class="px-3 py-4 text-center">
                    <p class="text-xl font-extrabold text-emerald-600">{{ $quizPassed }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Quiz<br>Lulus</p>
                </div>
                <div class="px-3 py-4 text-center">
                    <p class="text-xl font-extrabold text-indigo-600">{{ $avgScore }}%</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Rata-rata<br>Skor</p>
                </div>
            </div>
            {{-- pass rate bar --}}
            @php $passRate = $quizTotal > 0 ? (int) round($quizPassed / $quizTotal * 100) : 0; @endphp
            <div class="px-4 pb-4">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[10px] text-slate-400">Pass rate</p>
                    <p class="text-[10px] font-bold text-emerald-600">{{ $passRate }}%</p>
                </div>
                <div class="h-1.5 w-full rounded-full bg-slate-100">
                    <div class="h-1.5 rounded-full bg-emerald-400 transition-all"
                         style="width: {{ $passRate }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
