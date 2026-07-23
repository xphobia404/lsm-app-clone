{{-- resources/views/user/quizzes/result.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-slate-50 pb-16">

        {{-- Header --}}
        <div class="sticky top-0 z-40 bg-white border-b border-slate-100 px-4 py-3 flex items-center gap-3">
            @if($learningSchema)
            <a href="{{ route('user.schemas.show', $learningSchema) }}"
               class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            @endif
            <p class="text-sm font-bold text-slate-800">Hasil Quiz</p>
        </div>

        @php
            $total    = $quizzes->count();
            $scorePct = $total > 0 ? round(($correctCount / $total) * 100) : 0;
        @endphp

        <div class="px-4 pt-5 space-y-4 max-w-2xl mx-auto">

            {{-- Score Card --}}
            <div class="rounded-2xl text-center px-5 py-10 text-white"
                 style="background:linear-gradient(135deg,{{ $passed ? '#10b981,#059669' : '#ef4444,#dc2626' }})">

                {{-- Icon --}}
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/20 mx-auto">
                    @if($passed)
                    <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    @else
                    <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    @endif
                </div>

                <p class="text-white font-extrabold text-xl">
                    {{ $passed ? 'Selamat! 🎉' : 'Belum Lulus' }}
                </p>

                {{-- Score besar --}}
                <div class="mt-5 mb-1">
                    <span class="text-6xl font-black tabular-nums">{{ $scorePct }}%</span>
                </div>
                <p class="text-white/70 text-sm mb-5">{{ $correctCount }} dari {{ $total }} soal benar</p>

                {{-- Progress bar --}}
                <div class="mx-auto max-w-xs mb-5">
                    <div class="h-3 w-full rounded-full bg-white/20 overflow-hidden">
                        <div id="score-bar"
                             class="h-3 rounded-full bg-white transition-all duration-1000 ease-out"
                             style="width: 0%"
                             data-target="{{ $scorePct }}"></div>
                    </div>
                </div>

                {{-- Badge --}}
                <div class="inline-flex items-center gap-1.5 rounded-full bg-white/20 text-white px-4 py-1.5 text-xs font-bold">
                    {{ $passed ? 'LULUS ✓' : 'BELUM LULUS' }}
                    &nbsp;•&nbsp; Nilai Lulus: 100%
                </div>
            </div>

            {{-- Action Button --}}
            @if($passed)
                @if($learningSchema)
                <a href="{{ route('user.schemas.show', $learningSchema) }}"
                   class="w-full flex items-center justify-center gap-2 rounded-2xl py-3.5 text-sm font-bold text-white transition active:opacity-80"
                   style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                    Kembali ke Materi
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endif
            @else
                <a href="{{ route('user.quizzes.index', $section) }}"
                   class="w-full flex items-center justify-center gap-2 rounded-2xl py-3.5 text-sm font-bold text-white transition active:opacity-80"
                   style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Coba Lagi
                </a>
            @endif

        </div>
    </div>

    {{-- Animate progress bar on load --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bar = document.getElementById('score-bar');
            if (bar) {
                setTimeout(function () {
                    bar.style.width = bar.dataset.target + '%';
                }, 300);
            }
        });
    </script>
</x-app-layout>
