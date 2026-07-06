{{-- resources/views/user/schemas/index.blade.php --}}
<x-app-layout title="Materi">
<div class="pb-12">

    {{-- ============================================================
         HEADER
    ============================================================ --}}
    <div class="relative overflow-hidden px-5 pt-6 pb-6"
         style="background: linear-gradient(135deg, #0f2460 0%, #1e3a8a 60%, #1d4ed8 100%)">
        <div class="absolute -top-4 -right-4 h-24 w-24 rounded-full opacity-10" style="background:#fff"></div>
        <div class="absolute top-8 -right-1 h-12 w-12 rounded-full opacity-10" style="background:#fff"></div>

        <h1 class="text-base font-extrabold text-white">&#128218; Semua Materi</h1>
        <p class="mt-0.5 text-xs text-blue-200">Pilih materi untuk mulai atau lanjutkan belajar</p>

        {{-- Search --}}
        <form method="GET" class="mt-4">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-blue-300"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari materi..."
                       class="w-full rounded-xl border-0 bg-white/15 py-2.5 pl-9 pr-4
                              text-sm text-white placeholder-blue-300
                              focus:outline-none focus:ring-2 focus:ring-white/40 transition"
                       style="backdrop-filter:blur(4px)">
            </div>
        </form>
    </div>

    {{-- ============================================================
         SUMMARY CHIPS
    ============================================================ --}}
    @php
        $totalSchemas    = $schemas->total();
        $schemasWithProg = 0;
        $schemasComplete = 0;
        foreach ($schemas as $s) {
            $sids  = $s->sections->pluck('id');
            $total = $sids->count();
            if ($total === 0) continue;
            $done = $sids->filter(fn ($id) => $progressMap->get($id) === 'completed')->count();
            if ($done > 0) $schemasWithProg++;
            if ($done === $total) $schemasComplete++;
        }
    @endphp
    <div class="px-4 mt-4 flex gap-2 overflow-x-auto pb-1 scrollbar-none">
        <div class="shrink-0 flex items-center gap-1.5 rounded-full bg-white border border-slate-100 shadow-sm px-3 py-1.5">
            <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
            <span class="text-[11px] font-semibold text-slate-700">{{ $totalSchemas }} materi</span>
        </div>
        <div class="shrink-0 flex items-center gap-1.5 rounded-full bg-white border border-slate-100 shadow-sm px-3 py-1.5">
            <span class="h-2 w-2 rounded-full bg-amber-400"></span>
            <span class="text-[11px] font-semibold text-slate-700">{{ $schemasWithProg }} berlangsung</span>
        </div>
        <div class="shrink-0 flex items-center gap-1.5 rounded-full bg-white border border-slate-100 shadow-sm px-3 py-1.5">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            <span class="text-[11px] font-semibold text-slate-700">{{ $schemasComplete }} selesai</span>
        </div>
    </div>

    {{-- ============================================================
         LIST MATERI
    ============================================================ --}}
    <div class="px-4 mt-4 space-y-3">
        @forelse($schemas as $schema)
        @php
            $sids    = $schema->sections->pluck('id');
            $total   = $sids->count();
            $done    = $sids->filter(fn ($id) => $progressMap->get($id) === 'completed')->count();
            $ongoing = $sids->filter(fn ($id) => $progressMap->get($id) === 'in_progress')->count();
            $pct     = $total > 0 ? (int) round($done / $total * 100) : 0;

            if ($pct >= 100) {
                $barColor    = '#10b981';
                $badgeBg     = 'bg-emerald-50';
                $badgeText   = 'text-emerald-700';
                $badgeLabel  = '&#10003; Selesai';
                $ringColor   = '#10b981';
            } elseif ($pct > 0 || $ongoing > 0) {
                $barColor    = '#f59e0b';
                $badgeBg     = 'bg-amber-50';
                $badgeText   = 'text-amber-700';
                $badgeLabel  = '&#9654; Berlangsung';
                $ringColor   = '#f59e0b';
            } else {
                $barColor    = '#6366f1';
                $badgeBg     = 'bg-slate-50';
                $badgeText   = 'text-slate-500';
                $badgeLabel  = 'Mulai';
                $ringColor   = '#c7d2fe';
            }
        @endphp

        <a href="{{ route('user.schemas.show', $schema) }}"
           class="flex items-stretch gap-0 rounded-2xl bg-white border border-slate-100 shadow-sm
                  overflow-hidden active:bg-slate-50 transition">

            {{-- Left accent bar --}}
            <div class="w-1 shrink-0" style="background: {{ $barColor }}"></div>

            <div class="flex-1 px-4 py-4">
                {{-- Title row --}}
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 leading-snug">
                            {{ $schema->title }}
                        </p>
                        @if($schema->description)
                        <p class="mt-0.5 text-xs text-slate-400 line-clamp-1">
                            {{ $schema->description }}
                        </p>
                        @endif
                    </div>
                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold
                                 {{ $badgeBg }} {{ $badgeText }}">{!! $badgeLabel !!}</span>
                </div>

                {{-- Progress bar --}}
                <div class="mt-3">
                    <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-1.5 rounded-full transition-all"
                             style="width: {{ $pct }}%; background: {{ $barColor }}"></div>
                    </div>
                </div>

                {{-- Stats row --}}
                <div class="mt-1.5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-slate-400">{{ $total }} section</span>
                        @if($done > 0)
                        <span class="text-[10px] font-medium" style="color: {{ $barColor }}">{{ $done }} selesai</span>
                        @endif
                        @if($ongoing > 0)
                        <span class="text-[10px] text-amber-500">{{ $ongoing }} berjalan</span>
                        @endif
                    </div>
                    <span class="text-[10px] font-extrabold" style="color: {{ $barColor }}">{{ $pct }}%</span>
                </div>
            </div>

            {{-- Arrow --}}
            <div class="flex items-center pr-3">
                <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
        @empty
        <div class="rounded-2xl bg-slate-50 p-10 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-3 h-10 w-10 text-slate-300"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
            </svg>
            <p class="text-sm font-medium text-slate-500">Belum ada materi tersedia</p>
            <p class="mt-1 text-xs text-slate-400">Cek kembali nanti</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($schemas->hasPages())
    <div class="mt-6 px-4">
        {{ $schemas->links() }}
    </div>
    @endif

</div>
</x-app-layout>
