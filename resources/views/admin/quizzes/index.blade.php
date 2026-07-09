<x-admin-layout title="Quiz – {{ $section->title }}">
<div class="px-4 pt-5 pb-10 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('admin.sections.index') }}" class="hover:text-indigo-600 transition">Sections</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-600 font-medium truncate">{{ $section->title }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-semibold">Quiz</span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-800">Kelola Quiz</h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Section: <span class="font-medium text-indigo-600">{{ $section->title }}</span>
            </p>
        </div>
        <a href="{{ route('admin.sections.quizzes.create', $section) }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Soal
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-2 rounded-2xl bg-green-50 border border-green-100 px-4 py-3 text-xs font-medium text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.sections.quizzes.index', $section) }}" class="flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[180px]">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari pertanyaan atau opsi..."
                   class="w-full rounded-full border border-slate-200 bg-white py-2 pl-8 pr-4 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
        </div>

        <select name="status" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
            <option value="">Semua Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
        </select>

        <select name="per_page" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
            <option value="10" {{ (string) request('per_page', $perPage ?? 25) === '10' ? 'selected' : '' }}>10 / halaman</option>
            <option value="15" {{ (string) request('per_page', $perPage ?? 25) === '15' ? 'selected' : '' }}>15 / halaman</option>
            <option value="25" {{ (string) request('per_page', $perPage ?? 25) === '25' ? 'selected' : '' }}>25 / halaman</option>
            <option value="50" {{ (string) request('per_page', $perPage ?? 25) === '50' ? 'selected' : '' }}>50 / halaman</option>
            <option value="100" {{ (string) request('per_page', $perPage ?? 25) === '100' ? 'selected' : '' }}>100 / halaman</option>
        </select>

        <button type="submit" class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
            Filter
        </button>

        @if(request()->hasAny(['search','status','per_page']))
            <a href="{{ route('admin.sections.quizzes.index', $section) }}"
               class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 hover:bg-slate-50 transition">
                Reset
            </a>
        @endif
    </form>

    {{-- Count --}}
    @if($quizzes->total())
        <p class="text-xs text-slate-400">
            Menampilkan {{ $quizzes->firstItem() }}–{{ $quizzes->lastItem() }} dari {{ $quizzes->total() }} soal
        </p>
    @endif

    {{-- List --}}
    <div class="space-y-3">
        @forelse($quizzes as $quiz)
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm">
                {{-- Row 1 --}}
                <div class="flex items-start gap-3 px-4 pt-3.5 pb-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-sm font-bold text-violet-600">
                        {{ $quiz->quiz_order }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                            <p class="text-sm font-bold text-slate-800 break-words">{{ $quiz->question }}</p>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $quiz->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $quiz->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                            <span class="shrink-0 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-medium text-indigo-600">
                                Jawaban: {{ strtoupper($quiz->correct_answer) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-1 mt-2">
                            @foreach($quiz->getOptions() as $key => $val)
                                <div class="rounded-xl px-3 py-2 text-xs {{ $key === $quiz->correct_answer ? 'bg-green-50 text-green-700 font-semibold' : 'bg-slate-50 text-slate-600' }}">
                                    <strong class="uppercase">{{ $key }}.</strong> {{ $val }}
                                </div>
                            @endforeach
                        </div>

                        @if($quiz->explanation)
                            <p class="mt-2 text-xs text-slate-400 line-clamp-2">
                                💡 {{ $quiz->explanation }}
                            </p>
                        @endif

                        <div class="mt-1.5 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-10h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $quiz->media_count }} media
                            </span>
                            <span class="text-[10px] text-slate-300">
                                Diperbarui {{ $quiz->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="mx-4 border-t border-slate-100"></div>

                {{-- Row 2 --}}
                <div class="flex items-center gap-2 overflow-x-auto px-4 py-2.5" style="scrollbar-width:none;-webkit-overflow-scrolling:touch">
                    <a href="{{ route('admin.sections.quizzes.show', [$section, $quiz]) }}"
                       class="inline-flex shrink-0 items-center gap-1 rounded-full bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 active:bg-slate-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Detail
                    </a>

                    <a href="{{ route('admin.sections.quizzes.edit', [$section, $quiz]) }}"
                       class="inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 active:bg-amber-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>

                    <form method="POST" action="{{ route('admin.sections.quizzes.toggle-active', [$section, $quiz]) }}" class="shrink-0">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $quiz->is_active ? 'bg-orange-50 text-orange-500 active:bg-orange-100' : 'bg-green-50 text-green-600 active:bg-green-100' }}">
                            {{ $quiz->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('admin.sections.quizzes.destroy', [$section, $quiz]) }}"
                          class="shrink-0"
                          onsubmit="return confirm('Hapus soal ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-500 active:bg-red-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <p class="mt-3 text-sm font-medium text-slate-500">Belum ada soal quiz</p>
                <a href="{{ route('admin.sections.quizzes.create', $section) }}"
                   class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Soal
                </a>
            </div>
        @endforelse
    </div>

    @if($quizzes->hasPages())
        <div>{{ $quizzes->links() }}</div>
    @endif

</div>
</x-admin-layout>