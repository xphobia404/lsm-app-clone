<x-admin-layout title="Detail Konten">
<div class="px-4 pt-5 pb-10 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('admin.sections.index') }}" class="hover:text-indigo-600 transition">Sections</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.sections.contents.index', $section) }}" class="hover:text-indigo-600 transition truncate">{{ $section->title }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-semibold truncate">{{ $content->title }}</span>
    </div>

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-800">{{ $content->title }}</h2>
            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                @php
                    $typeColor = match($content->content_type) {
                        'video' => 'bg-rose-50 text-rose-600',
                        'file'  => 'bg-amber-50 text-amber-600',
                        'url'   => 'bg-sky-50 text-sky-600',
                        default => 'bg-indigo-50 text-indigo-600',
                    };
                    $typeLabel = match($content->content_type) {
                        'video' => 'Video', 'file' => 'File', 'url' => 'URL', default => 'Text',
                    };
                @endphp
                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $typeColor }}">{{ $typeLabel }}</span>
                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold
                    {{ $content->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $content->is_active ? 'Aktif' : 'Non-aktif' }}
                </span>
                <span class="text-[10px] text-slate-400">Urutan: {{ $content->content_order }}</span>
            </div>
        </div>
        <a href="{{ route('admin.sections.contents.edit', [$section, $content]) }}"
           class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-100 transition shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
    </div>

    {{-- Body --}}
    @if($content->body)
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
            <h3 class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">Isi Konten</h3>
            <div class="prose prose-sm max-w-none text-slate-700">{!! nl2br(e($content->body)) !!}</div>
        </div>
    @endif

    {{-- URL --}}
    @if($content->url)
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
            <h3 class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">URL</h3>
            <a href="{{ $content->url }}" target="_blank" rel="noopener noreferrer"
               class="text-sm text-indigo-600 break-all hover:underline">{{ $content->url }}</a>
        </div>
    @endif

    {{-- Media --}}
    @if($content->media->isNotEmpty())
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
            <h3 class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">Media ({{ $content->media->count() }})</h3>
            <div class="space-y-2">
                @foreach($content->media as $m)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
                        <span class="text-xs font-semibold text-slate-500 w-12">{{ strtoupper($m->media_type) }}</span>
                        @if($m->url)
                            <a href="{{ $m->url }}" target="_blank" class="text-xs text-indigo-600 truncate hover:underline">{{ $m->title ?: $m->url }}</a>
                        @elseif($m->file_path)
                            <span class="text-xs text-slate-600 truncate">{{ $m->title ?: $m->file_path }}</span>
                        @endif
                        <span class="ml-auto text-[10px] rounded-full px-2 py-0.5
                            {{ $m->is_active ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400' }}">
                            {{ $m->is_active ? 'Aktif' : 'Non-aktif' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Meta --}}
    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3 text-xs text-slate-400 space-y-1">
        <p>Dibuat: {{ $content->created_at->translatedFormat('d F Y, H:i') }}</p>
        <p>Diperbarui: {{ $content->updated_at->translatedFormat('d F Y, H:i') }}</p>
    </div>

</div>
</x-admin-layout>
