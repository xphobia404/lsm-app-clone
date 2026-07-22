{{-- resources/views/user/schemas/show.blade.php --}}
<x-app-layout :title="$learningSchema->title">
<div class="px-4 pt-5 pb-10">

    {{-- Back --}}
    <div class="mb-4">
        <a href="{{ route('user.schemas.index') }}"
           class="inline-flex items-center gap-1 text-xs text-indigo-600 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Semua Materi
        </a>
    </div>

    {{-- Hero card --}}
    @php
        $sections = $learningSchema->sections;
        $total    = $sections->count();
        $done     = $sections->filter(fn($s) => $progressMap->get($s->id) === 'completed')->count();
        $pct      = $total > 0 ? round(($done / $total) * 100) : 0;
    @endphp

    <div class="mb-5 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 p-5 text-white shadow-lg">
        <h2 class="text-base font-extrabold leading-snug">{{ $learningSchema->title }}</h2>
        @if($learningSchema->description)
        <p class="mt-1 text-xs text-indigo-100 leading-relaxed">
            {{ $learningSchema->description }}
        </p>
        @endif

        {{-- Stats --}}
        <div class="mt-4 grid grid-cols-3 gap-2">
            <div class="rounded-xl bg-white/15 px-2 py-2 text-center">
                <p class="text-lg font-extrabold">{{ $total }}</p>
                <p class="text-[10px] text-indigo-100">Section</p>
            </div>
            <div class="rounded-xl bg-white/15 px-2 py-2 text-center">
                <p class="text-lg font-extrabold">{{ $done }}</p>
                <p class="text-[10px] text-indigo-100">Selesai</p>
            </div>
            <div class="rounded-xl bg-white/15 px-2 py-2 text-center">
                <p class="text-lg font-extrabold">{{ $pct }}%</p>
                <p class="text-[10px] text-indigo-100">Progress</p>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-3">
            <div class="h-2 w-full rounded-full bg-white/20">
                <div class="h-2 rounded-full bg-white transition-all"
                     style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    {{-- Section list --}}
    <div class="space-y-2">
        @forelse($sections as $i => $section)
        @php
            $status = $progressMap->get($section->id);

            // Sequential lock: section pertama selalu unlock,
            // section berikutnya hanya bisa dibuka jika section sebelumnya 'completed'
            if ($i === 0) {
                $isLocked = false;
            } else {
                $prevSection = $sections[$i - 1];
                $isLocked = $progressMap->get($prevSection->id) !== 'completed';
            }
        @endphp

        @if($isLocked)
        <div class="flex items-center gap-3 rounded-2xl bg-slate-50 border border-slate-100
                    px-4 py-3 opacity-60 cursor-not-allowed select-none">
        @else
        <a href="{{ route('user.sections.show', [$learningSchema, $section]) }}"
           class="flex items-center gap-3 rounded-2xl bg-white border border-slate-100
                  px-4 py-3 shadow-sm active:bg-slate-50 transition">
        @endif

            {{-- Nomor / status icon --}}
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                        {{ $isLocked
                            ? 'bg-slate-200'
                            : ($status === 'completed'
                                ? 'bg-emerald-100'
                                : ($status === 'in_progress' ? 'bg-amber-100' : 'bg-slate-100')) }}">
                @if($isLocked)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                @elseif($status === 'completed')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                @elseif($status === 'in_progress')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <span class="text-xs font-bold text-slate-400">{{ $i + 1 }}</span>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold {{ $isLocked ? 'text-slate-400' : 'text-slate-800' }} truncate">
                    {{ $section->title }}
                </p>
                <div class="flex items-center gap-2 mt-0.5">
                    @if($isLocked)
                    <span class="text-[10px] text-slate-400">Selesaikan section sebelumnya</span>
                    @else
                        @if($section->contents_count ?? 0)
                        <span class="text-[10px] text-slate-400">
                            {{ $section->contents_count }} konten
                        </span>
                        @endif
                        @if($section->quizzes_count ?? 0)
                        <span class="text-[10px] text-amber-500">
                            {{ $section->quizzes_count }} quiz
                        </span>
                        @endif
                        @if(!($section->contents_count ?? 0) && !($section->quizzes_count ?? 0))
                        <span class="text-[10px] text-slate-300">Belum ada konten</span>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Status badge --}}
            @if($isLocked)
            <span class="shrink-0 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-400">
                Terkunci
            </span>
            @elseif($status === 'completed')
            <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-600">
                Selesai
            </span>
            @elseif($status === 'in_progress')
            <span class="shrink-0 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-600">
                Lanjutkan
            </span>
            @else
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-4 w-4 shrink-0 text-slate-300"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            @endif

        @if($isLocked)
        </div>
        @else
        </a>
        @endif

        @empty
        <div class="rounded-2xl bg-slate-50 p-10 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-3 h-10 w-10 text-slate-300"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm font-medium text-slate-500">Belum ada section</p>
            <p class="mt-1 text-xs text-slate-400">Section akan ditambahkan segera</p>
        </div>
        @endforelse
    </div>

</div>
</x-app-layout>
