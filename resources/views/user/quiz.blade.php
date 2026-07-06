{{-- resources/views/user/quiz.blade.php --}}
<x-app-layout title="Quiz">
    <div class="px-4 pt-5 pb-10">
        <div class="mb-4 flex items-center gap-2">
            <a href="{{ route('user.sections.show', [$section->learningSchema, $section]) }}"
               class="text-xs text-indigo-600 font-medium">&larr; Kembali ke Section</a>
        </div>

        <div class="mb-6">
            <h2 class="text-base font-bold text-slate-800">Quiz: {{ $section->title }}</h2>
            <p class="text-xs text-slate-500">Passing score: {{ $section->passing_score ?? 70 }}%</p>
        </div>

        @if(session('quiz_result'))
            @php $result = session('quiz_result'); @endphp
            <div class="mb-6 rounded-2xl p-5 {{ $result['passed'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                <p class="text-sm font-bold {{ $result['passed'] ? 'text-green-700' : 'text-red-700' }}">
                    {{ $result['passed'] ? '🎉 Selamat! Kamu lulus!' : '😔 Belum lulus, coba lagi.' }}
                </p>
                <p class="mt-1 text-xs {{ $result['passed'] ? 'text-green-600' : 'text-red-500' }}">
                    Skor: {{ $result['score'] }}% ({{ $result['correct'] }}/{{ $result['total'] }} benar)
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('user.quizzes.submit', [$section->learningSchema, $section]) }}" id="quiz-form">
            @csrf
            <div class="space-y-5">
                @foreach($quizzes as $i => $quiz)
                    <div class="rounded-2xl bg-white border border-slate-100 p-4 shadow-sm">
                        <p class="mb-3 text-sm font-semibold text-slate-800">{{ $i + 1 }}. {{ $quiz->question }}</p>
                        <div class="space-y-2">
                            @foreach($quiz->getOptions() as $key => $opt)
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="answers[{{ $quiz->id }}]" value="{{ $key }}"
                                           class="accent-indigo-600">
                                    <span class="text-xs text-slate-700">{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                <button type="submit"
                        class="w-full rounded-2xl bg-indigo-600 py-3 text-sm font-semibold text-white shadow active:bg-indigo-700 transition">
                    Kumpulkan Jawaban
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
