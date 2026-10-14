<x-admin-layout title="Quiz – {{ $section->title }}">
<div x-data="{ showImportModal: false, showSettings: false }" class="px-4 pt-5 pb-10 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('admin.sections.index') }}" class="hover:text-indigo-600 transition">Sections</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-600 font-medium truncate">{{ $section->title }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-semibold">Quiz</span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-800">Kelola Quiz</h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Section: <span class="font-medium text-indigo-600">{{ $section->title }}</span>
                &bull; Nilai Lulus: <span class="font-semibold text-slate-700">{{ $section->passing_score ?? 70 }}%</span>
                @if($section->randomize_questions)
                    &bull; <span class="text-violet-600 font-medium">Soal Diacak</span>
                @endif
                @if($section->randomize_options)
                    &bull; <span class="text-emerald-600 font-medium">Jawaban Diacak</span>
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Tombol Pengaturan Kuis --}}
            <button type="button"
                    @click="showSettings = !showSettings"
                    class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-300 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Atur Nilai & Acak
            </button>

            {{-- Download Template CSV --}}
            <a href="{{ route('admin.sections.quizzes.template', $section) }}"
               title="Download contoh template CSV untuk import soal dan pilihan jawaban"
               class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-300 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Template CSV
            </a>

            {{-- Import Soal CSV --}}
            <button type="button"
                    @click="showImportModal = true"
                    class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-xs font-semibold text-indigo-700 shadow-sm hover:bg-indigo-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Soal
            </button>

            {{-- Export Hasil Kuis --}}
            <a href="{{ route('admin.sections.quizzes.export-attempts', $section) }}"
               title="Export hasil kuis yang dikerjakan user ke CSV"
               class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700 shadow-sm hover:bg-emerald-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Hasil ({{ $attemptsCount }})
            </a>

            {{-- Tambah Soal Manual --}}
            <a href="{{ route('admin.sections.quizzes.create', $section) }}"
               class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Soal
            </a>
        </div>
    </div>

    {{-- Flash & Alerts --}}
    @if(session('success'))
        <div class="flex items-center gap-2 rounded-2xl bg-green-50 border border-green-100 px-4 py-3 text-xs font-medium text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-2 rounded-2xl bg-red-50 border border-red-100 px-4 py-3 text-xs font-medium text-red-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if(session('import_errors') && is_array(session('import_errors')))
        <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 text-xs space-y-2">
            <div class="flex items-center gap-2 text-amber-800 font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Catatan Import CSV (Beberapa baris dilewati):</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-amber-700 max-h-40 overflow-y-auto pl-1">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Panel Pengaturan Kuis Section --}}
    <div x-show="showSettings"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         style="display: none;"
         class="rounded-2xl border border-indigo-100 bg-white shadow-sm p-4 md:p-5">
        <form method="POST" action="{{ route('admin.sections.quizzes.settings', $section) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Pengaturan Kuis Section Ini</h3>
                    <p class="text-xs text-slate-400">Atur skor minimal kelulusan dan acak urutan soal maupun pilihan jawaban.</p>
                </div>
                <button type="button" @click="showSettings = false" class="text-slate-400 hover:text-slate-600 text-xs">
                    Tutup &times;
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Passing Score --}}
                <div class="space-y-1">
                    <label for="passing_score_input" class="block text-xs font-semibold text-slate-700">
                        Minimal Skor Kelulusan (%)
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number"
                               id="passing_score_input"
                               name="passing_score"
                               min="0"
                               max="100"
                               required
                               value="{{ old('passing_score', $section->passing_score ?? 70) }}"
                               class="w-24 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-800 focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        <span class="text-xs font-bold text-slate-500">%</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Default: 70%. Rentang: 0 - 100%.</p>
                </div>

                {{-- Acak Pertanyaan --}}
                <div class="flex items-start gap-2.5 pt-1">
                    <input type="checkbox"
                           id="randomize_questions_input"
                           name="randomize_questions"
                           value="1"
                           {{ old('randomize_questions', $section->randomize_questions) ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 rounded accent-indigo-600">
                    <label for="randomize_questions_input" class="cursor-pointer">
                        <span class="block text-xs font-semibold text-slate-700">Acak Urutan Pertanyaan</span>
                        <span class="block text-[11px] text-slate-400 leading-tight">Urutan soal akan diacak otomatis setiap kali user membuka kuis.</span>
                    </label>
                </div>

                {{-- Acak Pilihan Jawaban --}}
                <div class="flex items-start gap-2.5 pt-1">
                    <input type="checkbox"
                           id="randomize_options_input"
                           name="randomize_options"
                           value="1"
                           {{ old('randomize_options', $section->randomize_options) ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 rounded accent-indigo-600">
                    <label for="randomize_options_input" class="cursor-pointer">
                        <span class="block text-xs font-semibold text-slate-700">Acak Urutan Pilihan Jawaban</span>
                        <span class="block text-[11px] text-slate-400 leading-tight">Urutan pilihan A, B, C, D diacak untuk setiap pertanyaan.</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="showSettings = false" class="rounded-full px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="rounded-full bg-indigo-600 px-5 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    {{-- Modal Import Soal CSV --}}
    <div x-show="showImportModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
         style="display: none;">
        <div @click.away="showImportModal = false"
             class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Import Soal Kuis via CSV</h3>
                        <p class="text-xs text-slate-400">Tambahkan banyak soal sekaligus dengan cepat.</p>
                    </div>
                </div>
                <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.sections.quizzes.import', $section) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3.5 space-y-2 text-xs text-slate-600">
                    <p class="font-semibold text-slate-800">Petunjuk Format CSV:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-500">
                        <li>Kolom wajib: <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-indigo-600 font-semibold">question</code>, <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-indigo-600 font-semibold">option_a</code>, <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-indigo-600 font-semibold">option_b</code>, <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-indigo-600 font-semibold">correct_answer</code></li>
                        <li>Kolom opsional: <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-slate-700">option_c</code>, <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-slate-700">option_d</code>, <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-slate-700">explanation</code></li>
                        <li>Isi kunci jawaban <code class="bg-white px-1 py-0.5 rounded border border-slate-200 font-semibold">correct_answer</code> dengan huruf: <strong>A, B, C, atau D</strong>.</li>
                    </ul>
                    <div class="pt-1">
                        <a href="{{ route('admin.sections.quizzes.template', $section) }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-semibold text-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh Contoh File Template CSV
                        </a>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="csv_file" class="block text-xs font-semibold text-slate-700">Pilih File CSV (.csv)</label>
                    <input type="file"
                           id="csv_file"
                           name="csv_file"
                           accept=".csv,text/csv,text/plain"
                           required
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400">Ukuran file maksimal 4MB.</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showImportModal = false" class="rounded-full px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="rounded-full bg-indigo-600 px-5 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                        Upload & Import Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

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