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

        <div class="px-4 pt-5 space-y-4 max-w-2xl mx-auto">

            {{-- Score card --}}
            <div class="rounded-2xl text-center px-5 py-8"
                 style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                <p class="text-white/70 text-xs font-semibold mb-1">Skor Kamu</p>
                <p class="text-5xl font-extrabold text-white">{{ $percentage }}<span class="text-2xl">%</span></p>
                <p class="mt-2 text-white/80 text-xs">
                    {{ $correctCount }} benar dari {{ $quizzes->count() }} soal
                </p>
                <div class="mt-4 inline-flex items-center gap-1.5 rounded-full px-4 py-1.5
                            {{ $percentage >= 70 ? 'bg-green-400/30 text-green-100' : 'bg-red-400/30 text-red-100' }}
                            text-xs font-bold">
                    @if($percentage >= 70)
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    LULUS
                    @else
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    BELUM LULUS
                    @endif
                </div>
            </div>

            {{-- Review per soal --}}
            <div class="space-y-3">
                @foreach($quizzes as $i => $quiz)
                @php $r = $results[$quiz->id]; @endphp
                <div class="rounded-2xl bg-white border shadow-sm px-5 py-4
                            {{ $r['is_correct'] ? 'border-green-200' : 'border-red-200' }}">
                    <div class="flex items-start gap-2 mb-2">
                        <span class="flex h-5 w-5 shrink-0 mt-0.5 items-center justify-center rounded-full
                                     {{ $r['is_correct'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}
                                     text-[10px] font-extrabold">
                            {{ $r['is_correct'] ? '✓' : '✗' }}
                        </span>
                        <p class="text-sm font-semibold text-slate-800">{{ $quiz->question }}</p>
                    </div>

                    <div class="space-y-1 mb-3">
                        @foreach(['a','b','c','d'] as $opt)
                        @php $val = $quiz->{'option_'.$opt}; @endphp
                        @if($val)
                        <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs
                            @if($opt === $quiz->correct_answer) bg-green-50 text-green-700 font-semibold
                            @elseif($opt === $r['user_answer'] && !$r['is_correct']) bg-red-50 text-red-600
                            @else text-slate-500 @endif">
                            <span class="font-bold">{{ strtoupper($opt) }}.</span>
                            {{ $val }}
                            @if($opt === $quiz->correct_answer)
                            <span class="ml-auto text-green-500 font-bold">✓</span>
                            @elseif($opt === $r['user_answer'] && !$r['is_correct'])
                            <span class="ml-auto text-red-400">✗</span>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    </div>

                    @if($r['explanation'])
                    <div class="rounded-xl bg-amber-50 border border-amber-100 px-3 py-2">
                        <p class="text-[10px] font-bold text-amber-700 mb-0.5">Penjelasan</p>
                        <p class="text-xs text-amber-800">{{ $r['explanation'] }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-col gap-3 pt-2">
                <a href="{{ route('user.quizzes.index', $section) }}"
                   class="w-full flex items-center justify-center gap-2 rounded-2xl border border-indigo-200 py-3.5 text-sm font-semibold text-indigo-600 bg-white transition active:bg-indigo-50">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Coba Lagi
                </a>
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
            </div>

        </div>
    </div>
</x-app-layout>
