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

    <form method="POST" action="{{ route('admin.sections.contents.store', $section) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- ── Informasi Konten ── --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 space-y-4">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Informasi Konten</h3>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400 @error('title') border-red-400 @enderror"
                       placeholder="Judul konten">
                @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Konten <span class="text-red-500">*</span></label>
                <select name="content_type" id="contentType" required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
                    <option value="text"  {{ old('content_type','text') === 'text'  ? 'selected' : '' }}>Text / Artikel</option>
                    <option value="video" {{ old('content_type') === 'video' ? 'selected' : '' }}>Video (URL)</option>
                    <option value="file"  {{ old('content_type') === 'file'  ? 'selected' : '' }}>File / Dokumen</option>
                    <option value="url"   {{ old('content_type') === 'url'   ? 'selected' : '' }}>Link Eksternal</option>
                </select>
            </div>

            <div id="fieldBody">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Isi Konten</label>
                <textarea name="body" rows="6"
                          class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400"
                          placeholder="Tulis isi konten di sini...">{{ old('body') }}</textarea>
            </div>

            <div id="fieldUrl" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">URL</label>
                <input type="url" name="url" value="{{ old('url') }}"
                       class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400"
                       placeholder="https://...">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Urutan</label>
                    <input type="number" name="content_order" value="{{ old('content_order') }}" min="0"
                           class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-indigo-400 focus:outline-none"
                           placeholder="Otomatis">
                </div>
                <div class="flex items-end pb-2.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active','1') == '1' ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-400">
                        <span class="text-xs font-medium text-slate-700">Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Media ── --}}
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Media Lampiran</h3>
                <button type="button" id="btnAddMedia"
                        class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 active:bg-indigo-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Media
                </button>
            </div>

            <div id="mediaList" class="space-y-3"></div>

            <p id="mediaEmpty" class="text-xs text-slate-400 italic">Belum ada media. Klik "Tambah Media" untuk menambahkan.</p>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
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
(function () {
    let mediaIndex = 0;
    const list       = document.getElementById('mediaList');
    const empty      = document.getElementById('mediaEmpty');
    const btnAdd     = document.getElementById('btnAddMedia');
    const typeSelect = document.getElementById('contentType');
    const fieldBody  = document.getElementById('fieldBody');
    const fieldUrl   = document.getElementById('fieldUrl');

    // Toggle body/url field
    function toggleFields() {
        const v = typeSelect.value;
        fieldBody.classList.toggle('hidden', v !== 'text');
        fieldUrl.classList.toggle('hidden',  v === 'text');
    }
    typeSelect.addEventListener('change', toggleFields);
    toggleFields();

    function updateEmpty() {
        empty.classList.toggle('hidden', list.children.length > 0);
    }

    function buildRow(idx) {
        const wrap = document.createElement('div');
        wrap.className = 'media-row rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-3';
        wrap.dataset.idx = idx;
        wrap.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">Media #<span class="row-num">${list.children.length + 1}</span></span>
                <button type="button" class="btn-remove-media rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500 active:bg-red-100 transition">Hapus</button>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tipe Media *</label>
                    <select name="media[${idx}][media_type]" class="media-type-select w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                        <option value="audio">Audio</option>
                        <option value="url">URL</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Urutan</label>
                    <input type="number" name="media[${idx}][media_order]" value="${idx}" min="0"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul</label>
                <input type="text" name="media[${idx}][title]" placeholder="Judul media (opsional)"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
            </div>
            <div class="field-file">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Upload File</label>
                <input type="file" name="media[${idx}][file]"
                       class="w-full text-xs text-slate-500 file:mr-3 file:rounded-full file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-600">
            </div>
            <div class="field-url hidden">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">URL</label>
                <input type="url" name="media[${idx}][url]" placeholder="https://..."
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                <textarea name="media[${idx}][description]" rows="2" placeholder="Deskripsi (opsional)"
                          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none"></textarea>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="media[${idx}][is_active]" value="0">
                <input type="checkbox" name="media[${idx}][is_active]" value="1" checked
                       class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600">
                <span class="text-[11px] font-medium text-slate-600">Aktif</span>
            </label>
        `;

        const sel = wrap.querySelector('.media-type-select');
        function toggleMedia() {
            const isUrl = sel.value === 'url';
            wrap.querySelector('.field-file').classList.toggle('hidden',  isUrl);
            wrap.querySelector('.field-url').classList.toggle('hidden', !isUrl);
        }
        sel.addEventListener('change', toggleMedia);

        wrap.querySelector('.btn-remove-media').addEventListener('click', () => {
            wrap.remove();
            renumberRows();
            updateEmpty();
        });

        return wrap;
    }

    function renumberRows() {
        list.querySelectorAll('.media-row .row-num').forEach((el, i) => el.textContent = i + 1);
    }

    btnAdd.addEventListener('click', () => {
        list.appendChild(buildRow(mediaIndex++));
        updateEmpty();
    });

    updateEmpty();
})();
</script>
</x-admin-layout>
