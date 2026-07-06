<x-admin-layout title="Kelola Learning Schema">
    <div class="px-4 pt-5 pb-10 space-y-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h2 class="text-base font-bold text-slate-800">Kelola Learning Schema</h2>
            </div>
            <a href="{{ route('admin.learning-schemas.create') }}"
               class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm active:bg-indigo-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Schema
            </a>
        </div>

        @if(session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        <div class="space-y-3">
            @forelse($learningSchemas as $ls)
                <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $ls->title }}</p>
                                <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $ls->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $ls->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </div>
                            @if($ls->description)
                                <p class="mt-0.5 text-xs text-slate-400">{{ Str::limit($ls->description, 100) }}</p>
                            @endif
                            <p class="mt-1 text-xs text-slate-400">{{ $ls->sections_count ?? $ls->sections->count() }} section</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1.5">
                            {{-- Sections --}}
                            <a href="{{ route('admin.learning-schemas.sections.index', $ls) }}"
                               class="rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 active:bg-indigo-100 transition">Sections</a>
                            {{-- Edit --}}
                            <a href="{{ route('admin.learning-schemas.edit', $ls) }}"
                               class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 active:bg-amber-100 transition">Edit</a>
                            {{-- Toggle active --}}
                            <form method="POST" action="{{ route('admin.learning-schemas.toggle-active', $ls) }}">
                                @csrf @method('POST')
                                <button type="submit"
                                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $ls->is_active ? 'bg-red-50 text-red-500 active:bg-red-100' : 'bg-green-50 text-green-600 active:bg-green-100' }}">
                                    {{ $ls->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('admin.learning-schemas.destroy', $ls) }}"
                                  onsubmit="return confirm('Hapus learning schema ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-500 active:bg-red-100 transition">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-slate-50 p-8 text-center">
                    <p class="text-xs text-slate-400">Belum ada learning schema.</p>
                    <a href="{{ route('admin.learning-schemas.create') }}" class="mt-2 inline-block text-xs text-indigo-500 font-medium">+ Tambah sekarang</a>
                </div>
            @endforelse
        </div>

        @if($learningSchemas instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-4">{{ $learningSchemas->links() }}</div>
        @endif
    </div>
</x-admin-layout>
