{{-- resources/views/user/quizzes/result.blade.php --}}
<x-app-layout title="Hasil Quiz - {{ $section->title }}">
<div class="px-4 pt-5 pb-10">

    {{-- Result Hero --}}
    <div class="mb-6 rounded-2xl p-5 text-center shadow-md
        {{ $percentage >= ($section->passing_score ?? 70) ? 'bg-gradient-to-br from-emerald-400 to-teal-500' : 'bg-gradient-to-br from-rose-400 to-red-500' }}
        text-white">
        <div class="text-4xl mb-2">{{ $percentage >= ($section->passing_score ?? 70) ? '🎉' : '😔' }}</div>
        <p class="text-xl font-extrabold">{{ $percentage >= ($section->passing_score ?? 70) ? 'Selamat, Kamu Lulus!' : 'Belum Lulus' }}</p>
        <p class="text-sm opacity-90 mt-1">{{ $section->title }}</p>
        <div class="mt-4 inline-block rounded-2xl bg-white/20 px-6 py-3">
            <p class="text-4xl font-extrabold">{{ $percentage }}%</p>
            <p class="text-xs opacity-80">{{ $correctCount }} / {{ $quizzes->count() }} benar</p>
        </div>
        <p class="mt-3 text-xs opacity-80">Passing score: {{ $section->passing_score ?? 70 }}%</p>
    </div>

    {{-- Action Buttons --}}
    <div class="mb-6 flex gap-3">
        <a href="{{ route('user.quizzes.index', [$section->learningSchema, $section]) }}"
           class="flex-1 rounded-2xl border border-indigo-200 bg-indigo-50 py-3 text-center text-sm font-semibold text-indigo-700 active:bg-indigo-100 transition">
            Coba Lagi
        </a>
        <a href="{{ route('user.sections.show', [$section->learningSchema, $section]) }}"
           class="flex-1 rounded-2xl bg-indigo-600 py-3 text-center text-sm font-bold text-white shadow-sm active:bg-indigo-700 transition">
            Kembali ke Section
        </a>
    </div>

    {{-- Review Jawaban --}}
    <h2 class="mb-3 text-sm font-bold text-slate-700">Review Jawaban</h2>
    <div class="space-y-3">
        @foreach($quizzes as $i => $quiz)
        @php $res = $results[$quiz->id]; @endphp
        <div class="rounded-2xl bg-white border {{ $res['is_correct'] ? 'border-emerald-200' : 'border-rose-200' }} shadow-sm overflow-hidden">

            <div class="flex items-center gap-2 px-4 py-2.5 border-b {{ $res['is_correct'] ? 'border-emerald-100 bg-emerald-50' : 'border-rose-100 bg-rose-50' }}">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full
                    {{ $res['is_correct'] ? 'bg-emerald-500' : 'bg-rose-500' }} text-white">
                    @if($res['is_correct'])
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                </span>
                <p class="text-xs font-semibold {{ $res['is_correct'] ? 'text-emerald-700' : 'text-rose-700' }}">
                    Soal {{ $i + 1 }} &mdash; {{ $res['is_correct'] ? 'Benar' : 'Salah' }}
                </p>
            </div>

            <div class="p-4 space-y-2">
                <p class="text-sm font-semibold text-slate-800">{{ $quiz->question }}</p>

                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-600">
                        Jawabanmu: <span class="font-bold uppercase">{{ $res['user_answer'] ?? '—' }}</span>
                    </span>
                    @if(!$res['is_correct'])
                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-700">
                        Jawaban benar: <span class="font-bold uppercase">{{ $res['correct_answer'] }}</span>
                    </span>
                    @endif
                </div>

                @if($res['explanation'])
                <div class="rounded-xl bg-amber-50 border border-amber-100 px-3 py-2.5">
                    <p class="text-xs text-amber-700 leading-relaxed">
                        <span class="font-semibold">Penjelasan:</span> {{ $res['explanation'] }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>
</x-app-layout>
