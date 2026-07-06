<x-admin-layout :title="'Sections — ' . $learningSchema->title">
<div class="px-4 pt-5 pb-10 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('admin.learning-schemas.index') }}" class="hover:text-indigo-600 transition">Learning Schema</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-600 font-medium truncate">{{ Str::limit($learningSchema->title, 40) }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-500">Sections</span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-800">Kelola Section</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ $learningSchema->title }}</p>
        </div>
        <a href="{{ route('admin.learning-schemas.sections.create', $learningSchema) }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Section
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-2 rounded-2xl bg-green-50 border border-green-100 px-4 py-3 text-xs font-medium text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.learning-schemas.sections.index', $learningSchema) }}" class="flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[180px]">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul section..."
                   class="w-full rounded-full border border-slate-200 bg-white py-2 pl-8 pr-4 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
        </div>
        <select name="status" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
        </select>
        <button type="submit" class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.learning-schemas.sections.index', $learningSchema) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 hover:bg-slate-50 transition">Reset</a>
        @endif
    </form>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($sections as $section)
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="flex items-start gap-3 px-4 py-3.5">

                    {{-- Order Badge --}}
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white text-xs font-bold">
                        #{{ $section->section_order }}
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $section->title }}</p>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold
                                {{ $section->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $section->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </div>
                        @if($section->description)
                            <p class="text-xs text-slate-400 line-clamp-1">{{ $section->description }}</p>
                        @endif
                        <div class="mt-1.5 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ $section->contents_count ?? 0 }} konten
                            </span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                {{ $section->quizzes_count ?? 0 }} quiz
                            </span>
                            <span class="text-slate-300">•</span>
                            <span>Dibuat {{ $section->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex shrink-0 flex-wrap items-center gap-1.5">
                        {{-- Contents --}}
                        <a href="{{ route('admin.sections.contents.index', $section) }}"
                           class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-600 hover:bg-sky-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Konten
                        </a>
                        {{-- Quizzes --}}
                        <a href="{{ route('admin.sections.quizzes.index', $section) }}"
                           class="inline-flex items-center gap-1 rounded-full bg-violet-50 px-3 py-1.5 text-xs font-semibold text-violet-600 hover:bg-violet-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Quiz
                        </a>
                        {{-- Edit --}}
                        <a href="{{ route('admin.learning-schemas.sections.edit', [$learningSchema, $section]) }}"
                           class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        {{-- Toggle --}}
                        <form method="POST" action="{{ route('admin.learning-schemas.sections.toggle-active', [$learningSchema, $section]) }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold transition
                                    {{ $section->is_active ? 'bg-orange-50 text-orange-500 hover:bg-orange-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                {{ $section->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        {{-- Hapus --}}
                        <form method="POST" action="{{ route('admin.learning-schemas.sections.destroy', [$learningSchema, $section]) }}"
                              onsubmit="return confirm('Hapus section \'{{ addslashes($section->title) }}\' beserta semua konten dan quiz di dalamnya?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <p class="mt-3 text-sm font-medium text-slate-500">Belum ada section</p>
                <p class="mt-1 text-xs text-slate-400">Mulai dengan menambahkan section pertama.</p>
                <a href="{{ route('admin.learning-schemas.sections.create', $learningSchema) }}"
                   class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Section
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($sections->hasPages())
        <div class="mt-2">{{ $sections->links() }}</div>
    @endif

</div>
</x-admin-layout>
