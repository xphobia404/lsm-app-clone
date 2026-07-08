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

    <form method="POST" action="{{ route('admin.sections.contents.store', $section) }}"
          enctype="multipart/form-data" class="space-y-5" id="contentForm">
        @csrf

        {{-- Informasi Konten --}}
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
                <label class="block text-xs font-semibold text-slate-700 mb-1">Isi Konten</label>
                <x-quill-editor
                    name="body"
                    :value="old('body', '')"
                    form-id="contentForm"
                    placeholder="Tulis isi konten di sini..."
                />
                @error('body')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
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

        {{-- Media --}}
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
            <p id="mediaEmpty" class="text-xs text-slate-400 italic">Belum ada media. Klik &quot;Tambah Media&quot; untuk menambahkan.</p>
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
    const list   = document.getElementById('mediaList');
    const empty  = document.getElementById('mediaEmpty');
    const btn    = document.getElementById('btnAddMedia');
    const form   = document.getElementById('contentForm');

    const URL_TYPES   = ['youtube', 'google_drive'];
    const PLACEHOLDER = {
        youtube:      'https://www.youtube.com/watch?v=... atau https://youtu.be/...',
        google_drive: 'https://drive.google.com/file/d/.../view',
    };
    const LABEL = {
        youtube:      'Link YouTube',
        google_drive: 'Link Google Drive',
    };

    function buildRow() {
        const wrap = document.createElement('div');
        wrap.className = 'media-row rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-3';
        wrap.innerHTML = `
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">Media #<span class="row-num"></span></span>
                <button type="button" class="btn-rm rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-semibold text-red-500 active:bg-red-100">Hapus</button>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tipe Media *</label>
                    <select data-field="media_type" class="sel-type w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
                        <option value="image">&#128247; Gambar (Upload)</option>
                        <option value="video">&#127909; Video (Upload)</option>
                        <option value="audio">&#127925; Audio (Upload)</option>
                        <option value="youtube">&#128250; YouTube</option>
                        <option value="google_drive">&#128196; Google Drive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Urutan</label>
                    <input type="number" data-field="media_order" min="0"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul</label>
                <input type="text" data-field="title" placeholder="Judul media (opsional)"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
            </div>
            <div class="f-file">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Upload File</label>
                <input type="file" data-field="file"
                       class="w-full text-xs text-slate-500 file:mr-3 file:rounded-full file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-indigo-600">
            </div>
            <div class="f-url hidden">
                <label class="lbl-url block text-[11px] font-semibold text-slate-600 mb-1">URL</label>
                <input type="url" data-field="url" placeholder="https://"
                       class="inp-url w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi</label>
                <textarea data-field="description" rows="2" placeholder="Deskripsi (opsional)"
                          class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none"></textarea>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" data-field="is_active" value="0">
                <input type="checkbox" data-field="is_active" value="1" checked class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600">
                <span class="text-[11px] font-medium text-slate-600">Aktif</span>
            </label>
        `;

        const sel   = wrap.querySelector('.sel-type');
        const fFile = wrap.querySelector('.f-file');
        const fUrl  = wrap.querySelector('.f-url');
        const lbl   = wrap.querySelector('.lbl-url');
        const inp   = wrap.querySelector('.inp-url');

        sel.addEventListener('change', () => {
            const t = sel.value;
            const isUrl = URL_TYPES.includes(t);
            fFile.classList.toggle('hidden', isUrl);
            fUrl.classList.toggle('hidden', !isUrl);
            if (isUrl) { lbl.textContent = LABEL[t]; inp.placeholder = PLACEHOLDER[t]; }
        });

        wrap.querySelector('.btn-rm').addEventListener('click', () => {
            wrap.remove(); renumber(); updateEmpty();
        });

        return wrap;
    }

    function renumber() {
        list.querySelectorAll('.row-num').forEach((el, i) => el.textContent = i + 1);
    }

    function updateEmpty() {
        empty.classList.toggle('hidden', list.children.length > 0);
    }

    form.addEventListener('submit', function () {
        list.querySelectorAll('.media-row').forEach((row, i) => {
            row.querySelectorAll('[data-field]').forEach(el => {
                el.name = `media[${i}][${el.getAttribute('data-field')}]`;
            });
            const orderInput = row.querySelector('[data-field="media_order"]');
            if (orderInput && orderInput.value === '') orderInput.value = i;
        });
    });

    btn.addEventListener('click', () => {
        list.appendChild(buildRow()); renumber(); updateEmpty();
    });

    updateEmpty();
})();
</script>
</x-admin-layout>
