{{-- resources/views/user/quizzes/index.blade.php --}}
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
            <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-bold text-slate-800">Quiz: {{ $section->title }}</p>
                <p class="text-xs text-slate-400">{{ $quizzes->count() }} soal</p>
            </div>
        </div>

        <div class="px-4 pt-5 space-y-4 max-w-2xl mx-auto">

            {{-- Last attempt info --}}
            @if($lastAttempt)
            <div class="rounded-2xl bg-indigo-50 border border-indigo-100 px-4 py-3 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-indigo-700">Percobaan Terakhir</p>
                    <p class="text-xs text-indigo-500">
                        Skor: {{ $lastAttempt->correct_answers }}/{{ $lastAttempt->total_questions }}
                        &nbsp;&bull;&nbsp;
                        {{ $lastAttempt->attempted_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endif

            {{-- Quiz intro card --}}
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm px-5 py-6 text-center">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full mx-auto"
                     style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h2 class="text-base font-extrabold text-slate-800">{{ $section->title }}</h2>
                <p class="mt-1 text-xs text-slate-500">Jawab semua soal di bawah ini dengan benar.</p>

                <div class="mt-4 flex justify-center gap-6">
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-indigo-600">{{ $quizzes->count() }}</p>
                        <p class="text-[10px] text-slate-400">Total Soal</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-green-600">100%</p>
                        <p class="text-[10px] text-slate-400">Nilai Lulus</p>
                    </div>
                </div>
            </div>

            {{-- Quiz form --}}
            @if($quizzes->isEmpty())
            <div class="rounded-2xl bg-white border border-slate-100 px-5 py-10 text-center">
                <p class="text-sm text-slate-400">Belum ada soal quiz untuk section ini.</p>
            </div>
            @else
            <form action="{{ route('user.quizzes.submit', $section) }}" method="POST" id="quiz-form">
                @csrf
                <div class="space-y-4">
                    @foreach($quizzes as $i => $quiz)
                    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">

                        {{-- Nomor soal --}}
                        <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50 px-4 py-2.5">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">{{ $i + 1 }}</span>
                            <p class="text-xs text-slate-500">Soal {{ $i + 1 }} dari {{ $quizzes->count() }}</p>
                        </div>

                        <div class="px-4 py-4 space-y-3">
                            {{-- Pertanyaan --}}
                            <p class="text-sm font-semibold text-slate-800 leading-relaxed">{{ $quiz->question }}</p>

                            {{-- Gambar soal --}}
                            @if($quiz->activeMedia->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($quiz->activeMedia as $img)
                                <div class="rounded-xl overflow-hidden border border-slate-100">
                                    @if($img->file_path)
                                    <img src="{{ Storage::url($img->file_path) }}"
                                         alt="{{ $img->title ?: 'Gambar soal ' . ($i + 1) }}"
                                         loading="lazy"
                                         class="w-full max-h-64 object-contain bg-slate-50">
                                    @elseif($img->url)
                                    <img src="{{ $img->url }}"
                                         alt="{{ $img->title ?: 'Gambar soal ' . ($i + 1) }}"
                                         loading="lazy"
                                         class="w-full max-h-64 object-contain bg-slate-50">
                                    @endif
                                    @if($img->title)
                                    <p class="px-3 py-1.5 text-xs text-slate-500 bg-slate-50">{{ $img->title }}</p>
                                    @endif
                                    @if($img->description)
                                    <p class="px-3 pb-2 text-[10px] text-slate-400 italic">{{ $img->description }}</p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif

                            {{-- Pilihan jawaban --}}
                            <div class="space-y-2">
                                @foreach(['a','b','c','d'] as $opt)
                                @php $val = $quiz->{'option_'.$opt}; @endphp
                                @if($val)
                                <label class="flex items-start gap-3 cursor-pointer rounded-xl border border-slate-200 px-3 py-2.5 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50 transition">
                                    <input type="radio"
                                           name="answers[{{ $quiz->id }}]"
                                           value="{{ $opt }}"
                                           class="mt-0.5 accent-indigo-600">
                                    <span class="text-sm text-slate-700">{{ strtoupper($opt) }}. {{ $val }}</span>
                                </label>
                                @endif
                                @endforeach
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>

                <button type="submit"
                        class="mt-6 w-full flex items-center justify-center gap-2 rounded-2xl py-4 text-sm font-bold text-white shadow-lg transition active:opacity-80"
                        style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Kumpulkan Jawaban
                </button>
            </form>
            @endif

        </div>
    </div>
</x-app-layout>
