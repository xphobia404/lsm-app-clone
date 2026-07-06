{{-- resources/views/user/schemas/index.blade.php --}}
<x-app-layout title="Semua Materi">
<div class="px-4 pt-5 pb-10">

    {{-- Header --}}
    <div class="mb-4">
        <h2 class="text-base font-bold text-slate-800">Semua Materi</h2>
        <p class="text-xs text-slate-400 mt-0.5">Pilih materi untuk mulai belajar</p>
    </div>

    {{-- Search --}}
    <form method="GET" class="mb-4">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-300"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari materi..."
                   class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-4
                          text-sm text-slate-800 placeholder-slate-300 shadow-sm
                          focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 transition">
        </div>
    </form>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($schemas as $schema)
        @php
            $sectionIds  = $schema->sections->pluck('id') ?? collect();
            $totalSec    = $schema->sections_count ?? 0;
            $doneSec     = 0;
            foreach (($schema->sections ?? collect()) as $sec) {
                if (($progressMap[$sec->id]->first()?->status ?? null) === 'completed') $doneSec++;
            }
        @endphp

        <a href="{{ route('user.schemas.show', $schema) }}"
           class="block rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden
                  active:bg-slate-50 transition">
            <div class="px-4 py-3.5">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-800 leading-snug">
                            {{ $schema->title }}
                        </p>
                        @if($schema->description)
                        <p class="mt-0.5 text-xs text-slate-400 line-clamp-2">
                            {{ $schema->description }}
                        </p>
                        @endif
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-4 w-4 shrink-0 text-slate-300 mt-0.5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>

                {{-- Progress --}}
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] text-slate-400">Progress</span>
                        <span class="text-[10px] font-semibold text-slate-600">
                            {{ $doneSec }} / {{ $totalSec }} section
                        </span>
                    </div>
                    @php $pct = $totalSec > 0 ? round(($doneSec / $totalSec) * 100) : 0; @endphp
                    <div class="h-1.5 w-full rounded-full bg-slate-100">
                        <div class="h-1.5 rounded-full transition-all
                                    {{ $pct === 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
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
    <div class="mt-6">
        {{ $schemas->links() }}
    </div>
    @endif

</div>
</x-app-layout>
