<x-admin-layout :title="'Quiz — ' . $section->title">
    <div class="px-4 pt-5">

        @if(session('success'))
            <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
        @endif

        <div class="mb-1 flex items-center gap-2">
            <a href="{{ route('admin.sections.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Sections</a>
        </div>
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-800">Quiz Section</h2>
                <p class="text-xs text-slate-500">{{ $section->title }}</p>
            </div>
            <a href="{{ route('admin.sections.quizzes.create', $section) }}"
               class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm active:scale-[0.98] transition">
                + Tambah Soal
            </a>
        </div>

        @forelse($quizzes as $i => $quiz)
        <div class="mb-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="mb-2 text-sm font-semibold text-slate-800">
                <span class="mr-1.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ $i+1 }}</span>
                {{ $quiz->question }}
            </p>

            {{-- Media terlampir --}}
            @if($quiz->media->isNotEmpty())
            <div class="mb-3 grid grid-cols-3 gap-1.5">
                @foreach($quiz->media as $m)
                <div class="rounded-xl border border-slate-200 bg-slate-50 overflow-hidden">
                    @if($m->type === 'image')
                        <img src="{{ $m->url }}" alt="{{ $m->original_name }}"
                             class="h-20 w-full object-contain p-1">
                    @elseif($m->type === 'video')
                        <video src="{{ $m->url }}" controls
                               class="h-20 w-full object-contain bg-black"></video>
                    @elseif($m->type === 'audio')
                        <div class="flex flex-col items-center justify-center h-20 px-2">
                            <svg class="h-6 w-6 text-indigo-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                            <audio src="{{ $m->url }}" controls class="w-full"></audio>
                        </div>
                    @endif
                    <p class="truncate px-1.5 py-0.5 text-xs text-slate-400">{{ $m->original_name }}</p>
                </div>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-2 gap-1 mb-3">
                @foreach($quiz->getOptions() as $key => $val)
                <div class="rounded-xl px-3 py-2 text-xs
                             {{ $key === $quiz->correct_answer ? 'bg-green-50 text-green-700 font-semibold' : 'bg-slate-50 text-slate-600' }}">
                    <strong class="uppercase">{{ $key }}.</strong> {{ $val }}
                </div>
                @endforeach
            </div>

            @if($quiz->explanation)
            <p class="mb-3 text-xs text-slate-500 italic">💡 {{ $quiz->explanation }}</p>
            @endif

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sections.quizzes.edit', [$section, $quiz]) }}"
                   class="flex-1 rounded-full border border-slate-300 px-3 py-2 text-center text-xs font-medium text-slate-700 active:bg-slate-50 transition">Edit</a>
                <form method="POST" action="{{ route('admin.sections.quizzes.destroy', [$section, $quiz]) }}"
                      onsubmit="return confirm('Hapus soal ini?')">
                    @csrf @method('DELETE')
                    <button class="rounded-full border border-red-200 bg-red-50 px-3 py-2 text-xs font-medium text-red-600 active:bg-red-100 transition">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
            <p class="text-sm text-slate-400">Belum ada soal quiz. Tambahkan soal pertama!</p>
        </div>
        @endforelse
    </div>
</x-admin-layout>
