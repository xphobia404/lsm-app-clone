<x-app-layout title="Home">
<div class="px-4 pt-4 pb-4 space-y-5">

    @foreach(['success','error','info'] as $type)
        @if(session($type))
            <x-alert :type="$type" class="mb-0">{{ session($type) }}</x-alert>
        @endif
    @endforeach

    @php
        $sections = $sectionsMap->flatten(1);
    @endphp

    {{-- GREETING CARD --}}
    <div class="relative overflow-hidden rounded-2xl text-white px-5 py-5"
         style="background: linear-gradient(135deg, #0f2460 0%, #1e40af 100%);">
        <div class="absolute -top-6 -right-6 h-28 w-28 rounded-full" style="background:rgba(255,255,255,0.06);"></div>
        <div class="absolute bottom-0 left-16 h-16 w-16 rounded-full" style="background:rgba(255,255,255,0.04);"></div>
        <div class="relative z-10">
            <p class="text-blue-200 text-xs">Selamat datang kembali,</p>
            <h2 class="text-lg font-bold mt-0.5 leading-tight">{{ auth()->user()->name ?: auth()->user()->username }}</h2>
            @php
                $total     = $totalCount;
                $done      = $completedCount;
                $pct       = $total > 0 ? round(($done / $total) * 100) : 0;
                $remaining = $total - $done;
            @endphp
            <div class="mt-4">
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-blue-100">Progress keseluruhan</span>
                    <span class="font-bold">{{ $pct }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-white/20">
                    <div class="h-2 rounded-full bg-white transition-all duration-700" style="width:{{ $pct }}%;"></div>
                </div>
                <p class="mt-2 text-xs text-blue-200">{{ $done }} selesai &bull; {{ $remaining }} tersisa dari {{ $total }} section</p>
            </div>
        </div>
    </div>

    {{-- SPESIALISASI CHIPS --}}
    @if($courseTypes->isNotEmpty())
    <div class="flex flex-wrap gap-2">
        @foreach($courseTypes as $ct)
        <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            {{ $ct->name }}
        </span>
        @endforeach
    </div>
    @endif

    {{-- LANJUTKAN BELAJAR --}}
    @php
        $incompleteSections = $sections->filter(fn($s) => ($progressMap[$s->id]?->status ?? 'not_started') !== 'completed');
        $nextSection = $incompleteSections->first(fn($s) =>
            ($progressMap[$s->id]?->unlocked ?? false) || $sectionsMap->get($s->course_type_id)?->first()?->id === $s->id
        );
    @endphp

    @if($nextSection)
    @php
        $nst      = $progressMap[$nextSection->id]?->status ?? 'not_started';
        $nattempt = $attemptsMap[$nextSection->id] ?? null;
    @endphp
    <div>
        <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-1.5">
            @if($nst === 'in_progress')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Lanjutkan Belajar
            @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Mulai Sekarang
            @endif
        </h3>
        <a href="{{ route('user.section.show', $nextSection) }}"
           class="block rounded-2xl overflow-hidden shadow-md active:scale-[0.98] transition border border-slate-100">
            @if($nextSection->thumbnail_url)
                <img src="{{ $nextSection->thumbnail_url }}" alt="{{ $nextSection->title }}" class="h-36 w-full object-cover" loading="lazy">
            @else
                <div class="h-36 w-full flex items-center justify-center" style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            @endif
            <div class="bg-white px-4 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400">{{ $nextSection->courseType?->name }} &bull; Section {{ $nextSection->order }}</span>
                        <h4 class="text-sm font-bold text-slate-800 mt-0.5 leading-tight">{{ $nextSection->title }}</h4>
                    </div>
                    <div class="flex-shrink-0 ml-3 flex h-10 w-10 items-center justify-center rounded-full" style="background:linear-gradient(135deg,#1e3a8a,#2563eb);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
                @if($nattempt)
                <p class="mt-2 text-xs text-slate-400">Quiz: {{ $nattempt->total_attempts }}x &bull;
                    @if($nattempt->ever_passed) <span class="text-emerald-600 font-medium">Lulus ✓</span>
                    @else Best: {{ $nattempt->best_score }}% @endif
                </p>
                @endif
                <div class="mt-2.5 inline-flex w-full items-center justify-center gap-1.5 rounded-full py-2 text-xs font-semibold text-white" style="background:linear-gradient(135deg,#1e3a8a,#2563eb);">
                    {{ $nst === 'in_progress' ? 'Lanjutkan' : 'Mulai Belajar' }}
                </div>
            </div>
        </a>
    </div>
    @endif

    {{-- BELUM SELESAI (selain next) --}}
    @php
        $otherIncomplete = $incompleteSections
            ->filter(fn($s) => $nextSection && $s->id !== $nextSection->id)
            ->filter(fn($s) => $progressMap[$s->id]?->unlocked ?? false);
    @endphp
    @if($otherIncomplete->isNotEmpty())
    <div>
        <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7H9m3 4H9m5-4h.01M14 16h.01"/></svg>
            Belum Selesai
        </h3>
        <div class="space-y-2.5">
            @foreach($otherIncomplete as $section)
            @php $status = $progressMap[$section->id]?->status ?? 'not_started'; $attempt = $attemptsMap[$section->id] ?? null; @endphp
            <a href="{{ route('user.section.show', $section) }}" class="flex items-center gap-3 rounded-2xl bg-white border border-slate-100 px-4 py-3 shadow-sm active:scale-[0.98] transition">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-50">
                    <span class="text-sm font-black text-indigo-600">{{ $section->order }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-400">{{ $section->courseType?->name }} &bull; Section {{ $section->order }}</p>
                    <p class="text-sm font-semibold text-slate-800 leading-tight truncate">{{ $section->title }}</p>
                    @if($attempt)
                    <p class="text-xs text-slate-400 mt-0.5">Quiz {{ $attempt->total_attempts }}x @if($attempt->ever_passed) &bull; <span class="text-emerald-600">Lulus ✓</span> @else &bull; Best {{ $attempt->best_score }}% @endif</p>
                    @endif
                </div>
                <span class="shrink-0 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium border
                    {{ $status === 'in_progress' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-50 text-slate-400 border-slate-200' }}">
                    {{ $status === 'in_progress' ? 'On Going' : 'Belum' }}
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- SUDAH SELESAI --}}
    @php $completedSections = $sections->filter(fn($s) => ($progressMap[$s->id]?->status ?? '') === 'completed'); @endphp
    @if($completedSections->isNotEmpty())
    <div>
        <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Sudah Selesai ({{ $completedSections->count() }})
        </h3>
        <div class="space-y-2">
            @foreach($completedSections as $section)
            <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-100 px-4 py-3">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-emerald-600 font-medium">{{ $section->courseType?->name }} &bull; Section {{ $section->order }}</p>
                    <p class="text-sm font-semibold text-slate-700 leading-tight truncate">{{ $section->title }}</p>
                </div>
                <a href="{{ route('user.section.show', $section) }}" class="shrink-0 text-xs text-slate-400 underline">Lihat</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- EMPTY STATE --}}
    @if($sections->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p class="text-sm font-medium text-slate-500">Belum ada materi tersedia</p>
        <p class="text-xs text-slate-400 mt-1">Admin belum menambahkan section untuk spesialisasimu.</p>
    </div>
    @endif

</div>
</x-app-layout>
