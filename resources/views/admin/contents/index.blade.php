<x-admin-layout title="Konten – {{ $section->title }}">
<div class="px-4 pt-5 pb-10 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('admin.sections.index') }}" class="hover:text-indigo-600 transition">Sections</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-600 font-medium truncate">{{ $section->title }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-semibold">Konten</span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-800">Kelola Konten</h2>
            <p class="text-xs text-slate-400 mt-0.5">Section: <span class="font-medium text-indigo-600">{{ $section->title }}</span></p>
        </div>
        <a href="{{ route('admin.sections.contents.create', $section) }}"
           class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Konten
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
    <form method="GET" action="{{ route('admin.sections.contents.index', $section) }}" class="flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[160px]">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..."
                   class="w-full rounded-full border border-slate-200 bg-white py-2 pl-8 pr-4 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
        </div>
        <select name="type" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
            <option value="">Semua Tipe</option>
            <option value="text"  {{ request('type') === 'text'  ? 'selected' : '' }}>Text</option>
            <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
            <option value="file"  {{ request('type') === 'file'  ? 'selected' : '' }}>File</option>
            <option value="url"   {{ request('type') === 'url'   ? 'selected' : '' }}>URL</option>
        </select>
        <select name="status" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif</option>
        </select>
        <button type="submit" class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">Filter</button>
        @if(request()->hasAny(['search','type','status']))
            <a href="{{ route('admin.sections.contents.index', $section) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 hover:bg-slate-50 transition">Reset</a>
        @endif
    </form>

    {{-- Count --}}
    @if($contents->total())
        <p class="text-xs text-slate-400">Menampilkan {{ $contents->firstItem() }}–{{ $contents->lastItem() }} dari {{ $contents->total() }} konten</p>
    @endif

    {{-- List --}}
    <div class="space-y-3">
        @forelse($contents as $content)
            @php
                $typeColor = match($content->content_type) {
                    'video' => 'bg-rose-50 text-rose-600',
                    'file'  => 'bg-amber-50 text-amber-600',
                    'url'   => 'bg-sky-50 text-sky-600',
                    default => 'bg-indigo-50 text-indigo-600',
                };
                $typeLabel = match($content->content_type) {
                    'video' => 'Video',
                    'file'  => 'File',
                    'url'   => 'URL',
                    default => 'Text',
                };
            @endphp
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition">
                <div class="flex items-start gap-3 px-4 py-3.5">

                    {{-- Order badge --}}
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-sm font-bold text-slate-400">
                        {{ $content->content_order }}
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $content->title }}</p>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $typeColor }}">{{ $typeLabel }}</span>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold
                                {{ $content->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $content->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </div>
                        @if($content->url)
                            <p class="text-xs text-slate-400 truncate">{{ $content->url }}</p>
                        @elseif($content->body)
                            <p class="text-xs text-slate-400 line-clamp-1">{{ strip_tags($content->body) }}</p>
                        @endif
                        <p class="mt-1 text-[10px] text-slate-300">Diperbarui {{ $content->updated_at->diffForHumans() }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex shrink-0 flex-col gap-1.5">
                        <a href="{{ route('admin.sections.contents.show', [$section, $content]) }}"
                           class="inline-flex items-center gap-1 rounded-full bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Detail
                        </a>
                        <a href="{{ route('admin.sections.contents.edit', [$section, $content]) }}"
                           class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.sections.contents.toggle-active', [$section, $content]) }}">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-1 rounded-full px-3 py-1.5 text-xs font-semibold transition
                                {{ $content->is_active ? 'bg-orange-50 text-orange-500 hover:bg-orange-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                {{ $content->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.sections.contents.destroy', [$section, $content]) }}"
                              onsubmit="return confirm('Hapus konten \'{{ addslashes($content->title) }}\'?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-1 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="mt-3 text-sm font-medium text-slate-500">Belum ada konten</p>
                <a href="{{ route('admin.sections.contents.create', $section) }}"
                   class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Konten
                </a>
            </div>
        @endforelse
    </div>

    @if($contents->hasPages())
        <div>{{ $contents->links() }}</div>
    @endif

</div>
</x-admin-layout>
