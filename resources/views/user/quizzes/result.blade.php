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
            $total      = $quizzes->count();
            $scorePct   = $total > 0 ? round(($correctCount / $total) * 100) : 0;
        @endphp

        <div class="px-4 pt-5 space-y-4 max-w-2xl mx-auto">

            {{-- ===== SCORE CARD ===== --}}
            <div class="rounded-2xl text-center px-5 py-8 text-white"
                 style="background:linear-gradient(135deg,{{ $passed ? '#10b981,#059669' : '#ef4444,#dc2626' }})">

                {{-- Icon --}}
                <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-white/20 mx-auto">
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
                    {{ $passed ? 'Selamat! Semua Benar 🎉' : 'Belum Lulus' }}
                </p>
                <p class="mt-1 text-white/80 text-xs">
                    {{ $passed
                        ? 'Kamu menjawab semua soal dengan benar'
                        : 'Pelajari kembali materi dan coba lagi' }}
                </p>

                {{-- Score besar --}}
                <div class="mt-5 mb-2">
                    <span class="text-5xl font-black tabular-nums">{{ $scorePct }}%</span>
                </div>
                <p class="text-white/70 text-xs mb-4">{{ $correctCount }} dari {{ $total }} soal benar</p>

                {{-- Progress bar score --}}
                <div class="mx-auto max-w-xs">
                    <div class="h-3 w-full rounded-full bg-white/20 overflow-hidden">
                        <div id="score-bar"
                             class="h-3 rounded-full bg-white transition-all duration-1000 ease-out"
                             style="width: 0%"
                             data-target="{{ $scorePct }}"></div>
                    </div>
                </div>

                {{-- Badge lulus/gagal --}}
                <div class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-white/20 text-white px-4 py-1.5 text-xs font-bold">
                    {{ $passed ? 'LULUS ✓' : $correctCount . '/' . $total . ' Benar' }}
                    &nbsp;•&nbsp; Nilai Lulus: 100%
                </div>
            </div>

            {{-- ===== REVIEW SOAL ===== --}}
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 px-1">
                    Review Jawaban
                </p>
                <div class="space-y-3">
                    @foreach($quizzes as $i => $quiz)
                    @php $r = $results[$quiz->id]; @endphp
                    <div class="rounded-2xl bg-white border shadow-sm overflow-hidden
                                {{ $r['is_correct'] ? 'border-green-200' : 'border-red-200' }}">

                        {{-- Header soal --}}
                        <div class="flex items-center gap-2 px-4 py-2.5 border-b
                                    {{ $r['is_correct'] ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-extrabold
                                         {{ $r['is_correct'] ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700' }}">
                                {{ $r['is_correct'] ? '✓' : '✗' }}
                            </span>
                            <p class="text-xs font-semibold {{ $r['is_correct'] ? 'text-green-700' : 'text-red-700' }}">
                                Soal {{ $i + 1 }}
                                <span class="font-normal opacity-70">— {{ $r['is_correct'] ? 'Benar' : 'Salah' }}</span>
                            </p>
                        </div>

                        <div class="px-4 py-3 space-y-3">
                            {{-- Pertanyaan --}}
                            <p class="text-sm font-semibold text-slate-800 leading-relaxed">{{ $quiz->question }}</p>

                            {{-- Pilihan jawaban --}}
                            <div class="space-y-1.5">
                                @foreach(['a','b','c','d'] as $opt)
                                @php $val = $quiz->{'option_'.$opt}; @endphp
                                @if($val)
                                @php
                                    $isCorrectOpt = $opt === $quiz->correct_answer;
                                    $isUserPick   = $opt === $r['user_answer'];
                                    $bgClass      = match(true) {
                                        $isCorrectOpt                      => 'bg-green-50 border-green-300 text-green-800',
                                        $isUserPick && !$isCorrectOpt      => 'bg-red-50 border-red-300 text-red-700',
                                        default                            => 'bg-slate-50 border-slate-200 text-slate-500',
                                    };
                                @endphp
                                <div class="flex items-center gap-2 rounded-xl border px-3 py-2 text-xs {{ $bgClass }}">
                                    <span class="font-bold w-4 shrink-0">{{ strtoupper($opt) }}.</span>
                                    <span class="flex-1">{{ $val }}</span>
                                    @if($isCorrectOpt)
                                        <span class="shrink-0 text-green-600 font-bold">✓ Benar</span>
                                    @elseif($isUserPick && !$isCorrectOpt)
                                        <span class="shrink-0 text-red-500 font-bold">✗ Pilihanmu</span>
                                    @endif
                                </div>
                                @endif
                                @endforeach
                            </div>

                            {{-- Jawaban tidak dipilih --}}
                            @if(! $r['user_answer'])
                            <p class="text-[11px] text-slate-400 italic">⚠ Soal ini tidak dijawab</p>
                            @endif

                            {{-- Penjelasan --}}
                            @if($quiz->explanation)
                            <div class="rounded-xl bg-amber-50 border border-amber-100 px-3 py-2">
                                <p class="text-[10px] font-bold text-amber-700 mb-0.5">💡 Penjelasan</p>
                                <p class="text-xs text-amber-800">{{ $quiz->explanation }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ===== ACTION BUTTONS ===== --}}
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
