<x-app-layout :title="'Quiz — ' . $section->title">

    {{-- Progress Bar (sticky top, di bawah header) --}}
    <div class="sticky top-[57px] z-30 bg-white border-b border-slate-100 px-4 py-2">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-medium text-slate-500">Soal <span id="current-label">1</span> dari {{ $quizzes->count() }}</span>
            <span class="text-xs font-medium text-blue-600">Percobaan ke-{{ $attemptNumber }}</span>
        </div>
        <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
            <div id="progress-bar"
                 class="h-full rounded-full bg-blue-600 transition-all duration-300"
                 style="width: calc(1 / {{ $quizzes->count() }} * 100%)">
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('user.quiz.submit', $section) }}" id="quiz-form">
        @csrf

        {{-- Error global --}}
        @if(session('error') || $errors->any())
        <div class="mx-4 mt-4 rounded-2xl bg-red-50 border border-red-200 px-4 py-3">
            <p class="text-xs font-semibold text-red-600">
                {{ session('error') ?? 'Semua soal wajib dijawab sebelum submit.' }}
            </p>
        </div>
        @endif

        {{-- Soal-soal (hidden semua kecuali yang aktif) --}}
        <div class="px-4 pt-5 pb-32">
            @foreach($quizzes as $i => $quiz)
            <div class="quiz-slide {{ $i === 0 ? '' : 'hidden' }}" data-index="{{ $i }}">

                {{-- Nomor Soal --}}
                <div class="mb-4 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">
                        {{ $i + 1 }}
                    </span>
                    <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Pilihan Ganda</span>
                </div>

                {{-- Pertanyaan --}}
                <p class="mb-3 text-sm font-semibold text-slate-800 leading-relaxed">
                    {{ $quiz->question }}
                </p>

                {{-- Gambar Soal --}}
                @if($quiz->question_image)
                <div class="mb-5">
                    <img src="{{ Storage::url($quiz->question_image) }}"
                         alt="Gambar soal {{ $i + 1 }}"
                         class="w-full max-h-60 rounded-2xl border border-slate-200 object-contain bg-slate-50">
                </div>
                @endif

                {{-- Opsi Jawaban --}}
                <div class="space-y-3">
                    @foreach($quiz->getOptions() as $key => $label)
                    @php $inputId = "answer_{$quiz->id}_{$key}"; @endphp
                    <label for="{{ $inputId }}"
                           class="option-label flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-700 transition active:scale-[0.98] has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:text-blue-800 has-[:checked]:shadow-sm">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-slate-300 text-xs font-bold uppercase has-[:checked]:border-blue-500 transition">
                            {{ $key }}
                        </span>
                        <input type="radio"
                               id="{{ $inputId }}"
                               name="answers[{{ $quiz->id }}]"
                               value="{{ $key }}"
                               class="sr-only"
                               data-question-index="{{ $i }}"
                               onchange="onAnswered({{ $i }})"
                               {{ old("answers.{$quiz->id}") === $key ? 'checked' : '' }}
                        >
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>

                @error("answers.{$quiz->id}")
                    <p class="mt-3 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            @endforeach
        </div>

        {{-- Bottom Navigation (fixed) --}}
        <div class="fixed bottom-[72px] left-0 right-0 z-40 bg-white/95 backdrop-blur border-t border-slate-100 px-4 py-3">
            <div class="flex items-center gap-3">

                {{-- Previous --}}
                <button type="button" id="btn-prev"
                        onclick="navigate(-1)"
                        class="flex items-center justify-center gap-1.5 rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 shadow-sm active:bg-slate-50 transition disabled:opacity-30 disabled:pointer-events-none"
                        disabled>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Sebelumnya
                </button>

                {{-- Next / Submit --}}
                <button type="button" id="btn-next"
                        onclick="navigate(1)"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-blue-700 active:scale-[0.98] transition">
                    Selanjutnya
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Submit (hidden until last question) --}}
                <button type="submit" id="btn-submit"
                        class="hidden flex-1 items-center justify-center gap-1.5 rounded-full bg-green-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-green-700 active:scale-[0.98] transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Kirim Jawaban
                </button>

            </div>

            {{-- Dot indicators --}}
            <div class="mt-2.5 flex items-center justify-center gap-1.5">
                @foreach($quizzes as $i => $quiz)
                <button type="button"
                        onclick="goTo({{ $i }})"
                        class="dot h-2 w-2 rounded-full transition-all duration-200 {{ $i === 0 ? 'bg-blue-600 w-4' : 'bg-slate-300' }}"
                        data-index="{{ $i }}">
                </button>
                @endforeach
            </div>
        </div>

    </form>

    <script>
        const total    = {{ $quizzes->count() }};
        let current    = 0;
        const answered = new Array(total).fill(false);

        // Tandai soal yang sudah dijawab (dari old() saat error)
        document.querySelectorAll('input[type=radio]:checked').forEach(r => {
            answered[parseInt(r.dataset.questionIndex)] = true;
        });

        function onAnswered(index) {
            answered[index] = true;
            updateDot(index);
        }

        function goTo(index) {
            document.querySelectorAll('.quiz-slide').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.dot').forEach((dot, i) => {
                dot.classList.remove('bg-blue-600', 'bg-green-500', 'bg-slate-300', 'w-4');
                if (i === index) {
                    dot.classList.add('bg-blue-600', 'w-4');
                } else if (answered[i]) {
                    dot.classList.add('bg-green-500');
                } else {
                    dot.classList.add('bg-slate-300');
                }
            });

            document.querySelector(`.quiz-slide[data-index="${index}"]`).classList.remove('hidden');
            current = index;

            // Update label & progress bar
            document.getElementById('current-label').textContent = current + 1;
            document.getElementById('progress-bar').style.width = `${((current + 1) / total) * 100}%`;

            // Prev button
            document.getElementById('btn-prev').disabled = current === 0;

            // Next vs Submit
            const btnNext   = document.getElementById('btn-next');
            const btnSubmit = document.getElementById('btn-submit');
            if (current === total - 1) {
                btnNext.classList.add('hidden');
                btnSubmit.classList.remove('hidden');
                btnSubmit.classList.add('flex');
            } else {
                btnNext.classList.remove('hidden');
                btnSubmit.classList.add('hidden');
                btnSubmit.classList.remove('flex');
            }
        }

        function updateDot(index) {
            const dot = document.querySelector(`.dot[data-index="${index}"]`);
            if (dot && index !== current) {
                dot.classList.remove('bg-slate-300', 'bg-blue-600', 'w-4');
                dot.classList.add('bg-green-500');
            }
        }

        function navigate(dir) {
            const next = current + dir;
            if (next >= 0 && next < total) goTo(next);
        }

        // Init
        goTo(0);
    </script>

</x-app-layout>
