{{-- resources/views/user/quizzes/show.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-slate-50 pb-16">

        {{-- Header --}}
        <div class="sticky top-0 z-40 bg-white border-b border-slate-100 px-4 py-3 flex items-center gap-3">
            <a href="{{ route('user.quizzes.index', $section) }}"
               class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-bold text-slate-800">Soal {{ $currentIndex + 1 }} dari {{ $total }}</p>
                <p class="text-xs text-slate-400">{{ $section->title }}</p>
            </div>
            <span class="shrink-0 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-full px-2.5 py-1">
                {{ $currentIndex + 1 }}/{{ $total }}
            </span>
        </div>

        <div class="px-4 pt-5 max-w-2xl mx-auto">
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-5">
                <p class="text-xs font-bold text-indigo-500 mb-2">Soal {{ $currentIndex + 1 }}</p>
                <p class="text-sm font-semibold text-slate-800 mb-4">{{ $quiz->question }}</p>

                @if($quiz->activeMedia && $quiz->activeMedia->isNotEmpty())
                <div class="mb-4 space-y-2">
                    @foreach($quiz->activeMedia as $m)
                    @if($m->isImage())
                    <img src="{{ $m->getDisplayUrl() }}" alt="{{ $m->title ?? 'Media' }}"
                         class="rounded-xl w-full" loading="lazy">
                    @endif
                    @endforeach
                </div>
                @endif

                <div class="space-y-2">
                    @foreach(['a','b','c','d'] as $opt)
                    @php $val = $quiz->{'option_'.$opt}; @endphp
                    @if($val)
                    <div class="flex items-start gap-3 rounded-xl border border-slate-200 px-3 py-2.5">
                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-extrabold text-slate-500">{{ strtoupper($opt) }}</span>
                        <span class="text-sm text-slate-700">{{ $val }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Navigation --}}
            <div class="mt-5 flex gap-3">
                @if($prev)
                <a href="{{ route('user.quizzes.show', [$section, $prev]) }}"
                   class="flex-1 flex items-center justify-center gap-2 rounded-2xl border border-slate-200 py-3 text-sm font-semibold text-slate-600 bg-white transition active:bg-slate-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Sebelumnya
                </a>
                @endif
                @if($next)
                <a href="{{ route('user.quizzes.show', [$section, $next]) }}"
                   class="flex-1 flex items-center justify-center gap-2 rounded-2xl py-3 text-sm font-bold text-white transition active:opacity-80"
                   style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                    Berikutnya
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @else
                <a href="{{ route('user.quizzes.index', $section) }}"
                   class="flex-1 flex items-center justify-center gap-2 rounded-2xl py-3 text-sm font-bold text-white transition active:opacity-80"
                   style="background:linear-gradient(135deg,#10b981,#059669)">
                    Kerjakan Quiz
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
