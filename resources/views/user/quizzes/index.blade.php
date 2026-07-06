{{-- resources/views/user/quizzes/index.blade.php --}}
<x-app-layout title="Quiz - {{ $section->title }}">
<div class="px-4 pt-5 pb-10">

    {{-- Back --}}
    <div class="mb-4">
        <a href="{{ route('user.sections.show', [$section->learningSchema, $section]) }}"
           class="inline-flex items-center gap-1 text-xs text-indigo-600 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Section
        </a>
    </div>

    {{-- Header Card --}}
    <div class="mb-6 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 p-4 text-white shadow-md">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm">Quiz</p>
                <p class="text-xs text-amber-100 truncate">{{ $section->title }}</p>
            </div>
            <div class="text-right shrink-0">
                <p class="text-lg font-extrabold">{{ $quizzes->count() }}</p>
                <p class="text-xs text-amber-100">soal</p>
            </div>
        </div>
        <div class="mt-3">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-amber-100">Passing score</span>
                <span class="text-xs font-bold">{{ $section->passing_score ?? 70 }}%</span>
            </div>
            <div class="h-1.5 w-full rounded-full bg-white/20">
                <div class="h-1.5 rounded-full bg-white" id="progress-bar" style="width:0%"></div>
            </div>
            <p class="mt-1 text-right text-[10px] text-amber-100" id="progress-text">0 / {{ $quizzes->count() }} dijawab</p>
        </div>
    </div>

    {{-- Last Attempt Info --}}
    @if($lastAttempt)
    <div class="mb-4 rounded-2xl border {{ $lastAttempt->percentage >= ($section->passing_score ?? 70) ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50' }} p-3">
        <p class="text-xs font-semibold {{ $lastAttempt->percentage >= ($section->passing_score ?? 70) ? 'text-emerald-700' : 'text-slate-600' }}">
            Percobaan terakhir:
            {{ $lastAttempt->correct_answers }}/{{ $lastAttempt->total_questions }} benar
            &mdash;
            @php $pct = $lastAttempt->total_questions > 0 ? round(($lastAttempt->correct_answers / $lastAttempt->total_questions) * 100) : 0; @endphp
            {{ $pct }}%
            <span class="font-normal text-slate-400 ml-1">{{ $lastAttempt->attempted_at->diffForHumans() }}</span>
        </p>
    </div>
    @endif

    {{-- Form Soal --}}
    <form method="POST"
          action="{{ route('user.quizzes.submit', [$section->learningSchema, $section]) }}"
          id="quiz-form">
        @csrf

        <div class="space-y-4" id="quiz-list">
            @foreach($quizzes as $i => $quiz)
            @php $quiz->loadMissing('activeMedia'); @endphp

            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden"
                 data-question="{{ $quiz->id }}">

                <div class="flex items-center gap-2.5 border-b border-slate-100 px-4 py-2.5 bg-slate-50">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xs font-bold text-amber-700">{{ $i + 1 }}</span>
                    <p class="text-xs text-slate-500">Soal {{ $i + 1 }} dari {{ $quizzes->count() }}</p>
                    @if($quiz->activeMedia->count())
                    <span class="ml-auto flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-medium text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        {{ $quiz->activeMedia->count() }} media
                    </span>
                    @endif
                </div>

                <div class="p-4 space-y-3">
                    <p class="text-sm font-semibold text-slate-800 leading-relaxed">{{ $quiz->question }}</p>

                    @if($quiz->activeMedia->count())
                    <div class="space-y-2">
                        @foreach($quiz->activeMedia as $media)
                        @switch($media->media_type)
                        @case('image')
                        <div class="rounded-xl overflow-hidden border border-slate-100">
                            @if($media->file_path)
                            <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title ?: 'Gambar soal' }}" loading="lazy" class="w-full max-h-64 object-contain bg-slate-50">
                            @elseif($media->url)
                            <img src="{{ $media->url }}" alt="{{ $media->title ?: 'Gambar soal' }}" loading="lazy" class="w-full max-h-64 object-contain bg-slate-50">
                            @endif
                            @if($media->title)<p class="px-3 py-1.5 text-xs text-slate-500 bg-slate-50">{{ $media->title }}</p>@endif
                        </div>
                        @break
                        @case('video')
                        <div class="rounded-xl overflow-hidden border border-slate-100 bg-black">
                            @if($media->file_path)
                            <video controls class="w-full max-h-56" preload="metadata"><source src="{{ Storage::url($media->file_path) }}">Browser Anda tidak mendukung video.</video>
                            @elseif($media->url)
                                @php $ytId = null; if(preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})/',$media->url,$m)){$ytId=$m[1];} @endphp
                                @if($ytId)
                                <div class="relative w-full" style="padding-top:56.25%"><iframe class="absolute inset-0 h-full w-full" src="https://www.youtube.com/embed/{{ $ytId }}" frameborder="0" allowfullscreen></iframe></div>
                                @else
                                <video controls class="w-full max-h-56" preload="metadata"><source src="{{ $media->url }}">Browser Anda tidak mendukung video.</video>
                                @endif
                            @endif
                        </div>
                        @break
                        @case('audio')
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5">
                            @if($media->title)<p class="mb-1.5 text-xs font-medium text-slate-600">{{ $media->title }}</p>@endif
                            @if($media->file_path)
                            <audio controls class="w-full" preload="metadata"><source src="{{ Storage::url($media->file_path) }}">Browser tidak mendukung audio.</audio>
                            @elseif($media->url)
                            <audio controls class="w-full" preload="metadata"><source src="{{ $media->url }}">Browser tidak mendukung audio.</audio>
                            @endif
                        </div>
                        @break
                        @case('url')
                        @if($media->url)
                        <a href="{{ $media->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-2.5 active:bg-indigo-100 transition">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-indigo-700 truncate">{{ $media->title ?: 'Buka referensi' }}</p>
                                <p class="text-[10px] text-indigo-400 truncate">{{ $media->url }}</p>
                            </div>
                        </a>
                        @endif
                        @break
                        @endswitch
                        @endforeach
                    </div>
                    @endif

                    <div class="space-y-2" id="options-{{ $quiz->id }}">
                        @foreach($quiz->getOptions() as $key => $opt)
                        <label class="quiz-option flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5 cursor-pointer transition active:bg-indigo-50" data-qid="{{ $quiz->id }}">
                            <input type="radio" name="answers[{{ $quiz->id }}]" value="{{ $key }}" class="hidden quiz-radio" data-qid="{{ $quiz->id }}">
                            <span class="option-indicator flex h-7 w-7 shrink-0 items-center justify-center rounded-full border-2 border-slate-200 text-xs font-bold text-slate-400 uppercase transition">{{ $key }}</span>
                            <span class="text-sm text-slate-700 leading-snug">{{ $opt }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 space-y-2">
            <button type="submit" id="btn-submit" class="w-full rounded-2xl bg-indigo-600 py-3.5 text-sm font-bold text-white shadow-md active:bg-indigo-700 transition">
                Kumpulkan Jawaban
            </button>
            <p class="text-center text-xs text-slate-400" id="unanswered-hint"></p>
        </div>
    </form>

</div>

<style>
.quiz-option.selected { border-color: #6366f1; background-color: #eef2ff; }
.quiz-option.selected .option-indicator { border-color: #6366f1; background-color: #6366f1; color: #fff; }
</style>

<script>
(function () {
    const total = {{ $quizzes->count() }};
    const answered = {};
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const hint = document.getElementById('unanswered-hint');

    function updateProgress() {
        const count = Object.keys(answered).length;
        const pct = total > 0 ? Math.round((count / total) * 100) : 0;
        progressBar.style.width = pct + '%';
        progressText.textContent = count + ' / ' + total + ' dijawab';
        hint.textContent = count < total ? (total - count) + ' soal belum dijawab' : '✓ Semua soal sudah dijawab';
        hint.className = count < total ? 'text-center text-xs text-slate-400' : 'text-center text-xs text-emerald-600 font-medium';
    }

    document.getElementById('quiz-list').addEventListener('click', function (e) {
        const label = e.target.closest('.quiz-option');
        if (!label) return;
        const qid = label.dataset.qid;
        const radio = label.querySelector('.quiz-radio');
        if (!radio) return;
        document.querySelectorAll('.quiz-option[data-qid="' + qid + '"]').forEach(l => l.classList.remove('selected'));
        radio.checked = true;
        label.classList.add('selected');
        answered[qid] = radio.value;
        updateProgress();
    });

    updateProgress();
}());
</script>
</x-app-layout>
