<x-admin-layout title="Tambah Konten">
<div class="px-4 pt-5 pb-10 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('admin.sections.index') }}" class="hover:text-indigo-600 transition">Sections</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.sections.contents.index', $section) }}" class="hover:text-indigo-600 transition truncate">{{ $section->title }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-semibold">Tambah Konten</span>
    </div>

    <h2 class="text-base font-bold text-slate-800">Tambah Konten Baru</h2>

    <form method="POST" action="{{ route('admin.sections.contents.store', $section) }}" class="space-y-4">
        @csrf

        {{-- Title --}}
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400 @error('title') border-red-400 @enderror"
                   placeholder="Judul konten">
            @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Content Type --}}
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Konten <span class="text-red-500">*</span></label>
            <select name="content_type" id="contentType" required
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                <option value="text"  {{ old('content_type','text') === 'text'  ? 'selected' : '' }}>Text / Artikel</option>
                <option value="video" {{ old('content_type') === 'video' ? 'selected' : '' }}>Video (URL)</option>
                <option value="file"  {{ old('content_type') === 'file'  ? 'selected' : '' }}>File / Dokumen</option>
                <option value="url"   {{ old('content_type') === 'url'   ? 'selected' : '' }}>Link Eksternal</option>
            </select>
            @error('content_type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Body (text) --}}
        <div id="fieldBody">
            <label class="block text-xs font-semibold text-slate-700 mb-1">Isi Konten</label>
            <textarea name="body" rows="6"
                      class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400"
                      placeholder="Tulis isi konten di sini...">{{ old('body') }}</textarea>
            @error('body')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- URL --}}
        <div id="fieldUrl" class="hidden">
            <label class="block text-xs font-semibold text-slate-700 mb-1">URL</label>
            <input type="url" name="url" value="{{ old('url') }}"
                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400"
                   placeholder="https://...">
            @error('url')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Content Order --}}
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Urutan</label>
            <input type="number" name="content_order" value="{{ old('content_order') }}" min="0"
                   class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none"
                   placeholder="Kosongkan untuk otomatis">
        </div>

        {{-- is_active --}}
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-400">
            <span class="text-xs font-medium text-slate-700">Aktif</span>
        </label>

        {{-- Actions --}}
        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="flex-1 rounded-full bg-indigo-600 py-2.5 text-xs font-semibold text-white hover:bg-indigo-700 active:bg-indigo-800 transition">
                Simpan Konten
            </button>
            <a href="{{ route('admin.sections.contents.index', $section) }}"
               class="flex-1 rounded-full border border-slate-200 bg-white py-2.5 text-center text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
    </form>

</div>

<script>
    const typeSelect = document.getElementById('contentType');
    const fieldBody  = document.getElementById('fieldBody');
    const fieldUrl   = document.getElementById('fieldUrl');
    function toggleFields() {
        const v = typeSelect.value;
        fieldBody.classList.toggle('hidden', v !== 'text');
        fieldUrl.classList.toggle('hidden',  v === 'text');
    }
    typeSelect.addEventListener('change', toggleFields);
    toggleFields();
</script>
</x-admin-layout>
