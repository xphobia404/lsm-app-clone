<x-admin-layout title="Kelola Learning Schema">
<div class="px-4 pt-5 pb-10 space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h2 class="text-base font-bold text-slate-800">Kelola Learning Schema</h2>
        </div>
        <a href="{{ route('admin.learning-schemas.create') }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Tambah
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-2 rounded-2xl bg-green-50 border border-green-100 px-4 py-3 text-xs font-medium text-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.learning-schemas.index') }}" class="flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[160px]">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul schema..."
                   class="w-full rounded-full border border-slate-200 bg-white py-2 pl-8 pr-4 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
        </div>
        <select name="status" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
        </select>
        <button type="submit" class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.learning-schemas.index') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 hover:bg-slate-50 transition">Reset</a>
        @endif
    </form>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($learningSchemas as $ls)
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm">

            {{-- Row 1: info --}}
            <div class="px-4 pt-3.5 pb-3">
                <div class="flex flex-wrap items-center gap-2 mb-0.5">
                    <p class="text-sm font-bold text-slate-800 break-words">{{ $ls->title }}</p>
                    <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold
                        {{ $ls->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $ls->is_active ? 'Aktif' : 'Non-aktif' }}
                    </span>
                </div>
                @if($ls->description)
                    <p class="text-xs text-slate-400 line-clamp-2">{{ $ls->description }}</p>
                @endif
                <div class="mt-1.5 flex items-center gap-3">
                    <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        {{ $ls->sections_count ?? 0 }} section
                    </span>
                    <span class="text-xs text-slate-300">&bull;</span>
                    <span class="text-xs text-slate-400">{{ $ls->created_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- Divider --}}
            <div class="border-t border-slate-100 mx-4"></div>

            {{-- Row 2: actions (horizontal scrollable) --}}
            <div class="flex items-center gap-2 overflow-x-auto px-4 py-2.5"
                 style="scrollbar-width:none;-webkit-overflow-scrolling:touch">

                <a href="{{ route('admin.learning-schemas.sections.index', $ls) }}"
                   class="inline-flex shrink-0 items-center gap-1 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 active:bg-indigo-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Sections
                </a>

                <a href="{{ route('admin.learning-schemas.edit', $ls) }}"
                   class="inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 active:bg-amber-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>

                <form method="POST" action="{{ route('admin.learning-schemas.toggle-active', $ls) }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold transition
                            {{ $ls->is_active ? 'bg-orange-50 text-orange-500 active:bg-orange-100' : 'bg-green-50 text-green-600 active:bg-green-100' }}">
                        {{ $ls->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.learning-schemas.destroy', $ls) }}" class="shrink-0"
                      onsubmit="return confirm('Hapus learning schema \'{{ addslashes($ls->title) }}\' beserta semua section di dalamnya?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-500 active:bg-red-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </form>

            </div>
        </div>
        @empty
            <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="mt-3 text-sm font-medium text-slate-500">Belum ada learning schema</p>
                <p class="mt-1 text-xs text-slate-400">Mulai dengan menambahkan schema pertama kamu.</p>
                <a href="{{ route('admin.learning-schemas.create') }}"
                   class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Schema
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($learningSchemas->hasPages())
        <div class="mt-2">{{ $learningSchemas->links() }}</div>
    @endif

</div>
</x-admin-layout>
