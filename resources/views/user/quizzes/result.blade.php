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

            {{-- ===== PASSED ===== --}}
            @if($passed)

            {{-- Score card - lulus --}}
            <div class="rounded-2xl text-center px-5 py-8"
                 style="background:linear-gradient(135deg,#10b981,#059669)">
                <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-white/20 mx-auto">
                    <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-white font-extrabold text-xl">Selamat! Semua Benar 🎉</p>
                <p class="mt-1 text-white/80 text-xs">Kamu menjawab {{ $correctCount }} dari {{ $quizzes->count() }} soal dengan benar</p>
                <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/20 text-white px-4 py-1.5 text-xs font-bold">
                    LULUS • 100%
                </div>
            </div>

            {{-- Review soal - tampilkan jawaban benar jika passed --}}
            <div class="space-y-3">
                @foreach($quizzes as $i => $quiz)
                <div class="rounded-2xl bg-white border border-green-200 shadow-sm px-5 py-4">
                    <div class="flex items-start gap-2 mb-2">
                        <span class="flex h-5 w-5 shrink-0 mt-0.5 items-center justify-center rounded-full bg-green-100 text-green-600 text-[10px] font-extrabold">✓</span>
                        <p class="text-sm font-semibold text-slate-800">{{ $quiz->question }}</p>
                    </div>

                    <div class="space-y-1 mb-3">
                        @foreach(['a','b','c','d'] as $opt)
                        @php $val = $quiz->{'option_'.$opt}; @endphp
                        @if($val)
                        <div class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs
                            {{ $opt === $quiz->correct_answer ? 'bg-green-50 text-green-700 font-semibold' : 'text-slate-500' }}">
                            <span class="font-bold">{{ strtoupper($opt) }}.</span>
                            {{ $val }}
                            @if($opt === $quiz->correct_answer)
                            <span class="ml-auto text-green-500 font-bold">✓</span>
                            @endif
                        </div>
                        @endif
                        @endforeach
                    </div>

                    @if($quiz->explanation)
                    <div class="rounded-xl bg-amber-50 border border-amber-100 px-3 py-2">
                        <p class="text-[10px] font-bold text-amber-700 mb-0.5">Penjelasan</p>
                        <p class="text-xs text-amber-800">{{ $quiz->explanation }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Tombol kembali ke materi --}}
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

            {{-- ===== FAILED ===== --}}
            @else

            {{-- Score card - gagal --}}
            <div class="rounded-2xl text-center px-5 py-8"
                 style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-white/20 mx-auto">
                    <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <p class="text-white font-extrabold text-xl">Belum Lulus</p>
                <p class="mt-1 text-white/80 text-xs">
                    Kamu menjawab {{ $correctCount }} dari {{ $quizzes->count() }} soal dengan benar.
                    Harus 100% benar untuk lulus.
                </p>
                <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/20 text-white px-4 py-1.5 text-xs font-bold">
                    {{ $correctCount }}/{{ $quizzes->count() }} Benar
                </div>
            </div>

            {{-- Daftar soal - TANPA kisi-kisi jawaban --}}
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-4">
                <p class="text-sm font-bold text-slate-700 mb-1">Soal yang perlu diperbaiki</p>
                <p class="text-xs text-slate-400 mb-3">Pelajari kembali materi dan coba lagi. Jawaban yang benar tidak ditampilkan.</p>
                <div class="space-y-2">
                    @foreach($quizzes as $i => $quiz)
                    @php $r = $results[$quiz->id]; @endphp
                    <div class="flex items-start gap-2 rounded-xl px-3 py-2.5
                                {{ $r['is_correct'] ? 'bg-green-50' : 'bg-red-50' }}">
                        <span class="flex h-5 w-5 shrink-0 mt-0.5 items-center justify-center rounded-full
                                     {{ $r['is_correct'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}
                                     text-[10px] font-extrabold">
                            {{ $r['is_correct'] ? '✓' : '✗' }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold {{ $r['is_correct'] ? 'text-green-700' : 'text-red-700' }}">
                                Soal {{ $i + 1 }}
                            </p>
                            <p class="text-[11px] text-slate-500 truncate">{{ $quiz->question }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Tombol coba lagi --}}
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
</x-app-layout>
