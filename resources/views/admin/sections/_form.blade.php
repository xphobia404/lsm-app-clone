{{-- Shared form partial for create & edit section --}}

{{-- Quill CSS --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    .page-item { background:#f8fafc; border:1px solid #e2e8f0; border-radius:1rem; padding:1rem; margin-bottom:.75rem; }
    .page-item .ql-container { min-height:120px; border-radius:0 0 .5rem .5rem; }
    .page-item .ql-toolbar { border-radius:.5rem .5rem 0 0; }
    .slide-item { background:#f8fafc; border:1px solid #e2e8f0; border-radius:1rem; padding:1rem; margin-bottom:.75rem; }
    .slide-thumb-preview { width:100%; height:90px; object-fit:cover; border-radius:.5rem; margin-top:.5rem; }
    .audio-current { display:flex; align-items:center; gap:.5rem; padding:.5rem .75rem; background:#f0f4ff; border:1px solid #c7d2fe; border-radius:.5rem; margin-top:.25rem; }
    .audio-current span { font-size:.75rem; color:#4f46e5; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
</style>

@if($errors->any())
<x-alert type="error" class="mb-2">
    <ul class="list-disc list-inside text-xs space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</x-alert>
@endif

{{-- Spesialisasi Course --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Spesialisasi Course <span class="text-red-500">*</span></label>
    <select name="course_type_id"
            class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">-- Pilih Spesialisasi --</option>
        @foreach($courseTypes as $ct)
            <option value="{{ $ct->id }}"
                {{ old('course_type_id', $section->course_type_id ?? '') == $ct->id ? 'selected' : '' }}>
                {{ $ct->icon ?? '' }} {{ $ct->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- Judul --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Judul Section <span class="text-red-500">*</span></label>
    <input type="text" name="title" value="{{ old('title', $section->title ?? '') }}"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="Judul section" required>
</div>

{{-- Deskripsi --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Deskripsi</label>
    <textarea name="description" rows="2"
              class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Deskripsi singkat section">{{ old('description', $section->description ?? '') }}</textarea>
</div>

{{-- ═══════════════════ CONTENT MODE ═══════════════════ --}}
<div>
    <label class="mb-2 block text-xs font-semibold text-slate-700">Mode Konten <span class="text-red-500">*</span></label>
    <div class="grid grid-cols-2 gap-3">
        @php $currentMode = old('content_mode', $section->content_mode ?? 'single'); @endphp
        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 transition
                      has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50
                      border-slate-200">
            <input type="radio" name="content_mode" value="single"
                   {{ $currentMode === 'single' ? 'checked' : '' }}
                   class="sr-only" onchange="toggleContentMode()">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800">Single Page</p>
                <p class="text-xs text-slate-400">1 halaman konten seperti biasa</p>
            </div>
        </label>
        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-3 transition
                      has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50
                      border-slate-200">
            <input type="radio" name="content_mode" value="multi"
                   {{ $currentMode === 'multi' ? 'checked' : '' }}
                   class="sr-only" onchange="toggleContentMode()">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800">Multi Page</p>
                <p class="text-xs text-slate-400">Beberapa halaman, user bisa next/prev</p>
            </div>
        </label>
    </div>
</div>

{{-- ═══════════════════ SINGLE PAGE CONTENT ═══════════════════ --}}
<div id="content-single-wrap" {{ $currentMode === 'multi' ? 'class=hidden' : '' }}>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Konten / Materi Teks</label>
    <input type="hidden" name="content" id="content-input">
    <div id="quill-editor"
         style="min-height: 200px; background: white; border-radius: 0.75rem;"
         class="border border-slate-300">{!! old('content', $section->content ?? '') !!}</div>
    <p class="mt-1 text-xs text-slate-400">Gunakan toolbar untuk format teks. Kosongkan jika konten berupa media saja.</p>
</div>

{{-- ═══════════════════ MULTI PAGE BUILDER ═══════════════════ --}}
<div id="content-multi-wrap" {{ $currentMode !== 'multi' ? 'class=hidden' : '' }}>
    <div class="mb-3 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-700">Page Builder</p>
            <p class="text-xs text-slate-400">Tiap page bisa punya judul, konten teks kaya, foto, dan audio opsional.</p>
        </div>
        <button type="button" onclick="addPage()"
                class="flex items-center gap-1.5 rounded-full bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm active:scale-95 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Page
        </button>
    </div>

    @php
        $existingPages = !empty($section->pages) && is_array($section->pages) ? $section->pages : [];
    @endphp

    <div id="pages-container" class="space-y-3">
        @if(!empty($existingPages))
            @foreach($existingPages as $pi => $page)
            <div class="page-item" data-page-index="{{ $pi }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-xs font-black text-white page-num">{{ $pi + 1 }}</span>
                        <span class="text-xs font-bold text-slate-600">Page {{ $pi + 1 }}</span>
                    </div>
                    <button type="button" onclick="removePage(this)" class="text-slate-300 hover:text-red-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Page title --}}
                <input type="text" name="pages[{{ $pi }}][title]" value="{{ $page['title'] ?? '' }}"
                       placeholder="Judul page (opsional)"
                       class="mb-2 block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">

                {{-- Keep existing image --}}
                <input type="hidden" name="pages[{{ $pi }}][image_url]" value="{{ $page['image_url'] ?? '' }}">

                {{-- Page image --}}
                <div class="mb-2">
                    <label class="mb-1 block text-xs text-slate-500">Foto / Gambar Page <span class="text-slate-400">(opsional, jpg/png/webp maks 5MB)</span></label>
                    @if(!empty($page['image_url']))
                        <img src="{{ $page['image_url'] }}" class="slide-thumb-preview" loading="lazy">
                    @endif
                    <input type="file" name="pages[{{ $pi }}][new_image]" accept="image/jpeg,image/png,image/webp"
                           class="mt-1 block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:text-xs"
                           onchange="previewPageImage(this)">
                </div>

                {{-- ─── Audio per Page (NEW) ──────────────────────────────── --}}
                <div class="mb-2">
                    <label class="mb-1 block text-xs text-slate-500">
                        🎵 Audio Slide
                        <span class="text-slate-400">(opsional, mp3/wav/ogg maks 50MB)</span>
                    </label>

                    {{-- Tampilkan audio saat ini jika ada --}}
                    @if(!empty($page['audio_url']))
                        <div class="audio-current" id="audio-current-{{ $pi }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                            <span>{{ basename($page['audio_url']) }}</span>
                            <label class="flex items-center gap-1 text-xs text-red-400 cursor-pointer shrink-0">
                                <input type="checkbox" name="pages[{{ $pi }}][remove_audio]" value="1" class="h-3 w-3">
                                Hapus
                            </label>
                        </div>
                        <input type="hidden" name="pages[{{ $pi }}][audio_url]" value="{{ $page['audio_url'] }}">
                    @endif

                    <input type="file"
                           name="pages[{{ $pi }}][new_audio]"
                           accept="audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac"
                           class="mt-1 block w-full text-xs text-slate-500
                                  file:rounded-full file:border-0 file:bg-purple-50
                                  file:px-2 file:py-1 file:text-xs file:font-medium file:text-purple-700"
                           onchange="previewPageAudio(this, {{ $pi }})">
                    <div id="audio-new-preview-{{ $pi }}" class="hidden mt-1">
                        <audio controls class="w-full h-8" style="border-radius:.5rem;"></audio>
                        <p class="text-xs text-slate-400 mt-0.5">Preview audio baru</p>
                    </div>
                </div>
                {{-- ─── End Audio per Page ────────────────────────────────── --}}

                {{-- Page content via hidden Quill --}}
                <label class="mb-1 block text-xs text-slate-500">Konten Page <span class="text-red-400">*</span></label>
                <input type="hidden" name="pages[{{ $pi }}][content]" id="page-content-{{ $pi }}" value="{{ $page['content'] ?? '' }}">
                <div id="page-quill-{{ $pi }}" class="border border-slate-200" style="min-height:120px; background:white; border-radius:.5rem;">{!! $page['content'] ?? '' !!}</div>
            </div>
            @endforeach
        @endif
    </div>

    <input type="hidden" name="pages_count" id="pages-count" value="{{ count($existingPages) }}">
    <p class="mt-2 text-xs text-slate-400">Urutan page = urutan tampil ke user. Minimal 1 page untuk mode multi.</p>
</div>

{{-- ═══════════════════ TIPE MEDIA ═══════════════════ --}}
<div>
    <label class="mb-2 block text-xs font-semibold text-slate-700">Tipe Media <span class="text-slate-400">(opsional — tampil di atas konten)</span></label>
    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
        @php
            $mediaTypes = [
                'video_upload' => ['icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z', 'label' => 'Video', 'desc' => 'mp4 / webm'],
                'audio_upload' => ['icon' => 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3', 'label' => 'Audio', 'desc' => 'mp3 / wav'],
                'youtube'      => ['icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'YouTube', 'desc' => 'URL YouTube'],
                'drive'        => ['icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z', 'label' => 'Drive', 'desc' => 'Google Drive'],
            ];
            $currentType = old('media_type', $section->media_type ?? 'video_upload');
        @endphp
        @foreach($mediaTypes as $val => $meta)
        <label class="flex cursor-pointer flex-col items-center gap-1 rounded-2xl border px-3 py-3 text-center text-xs transition
                      has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:text-indigo-700
                      border-slate-200 text-slate-500">
            <input type="radio" name="media_type" value="{{ $val }}"
                   {{ $currentType === $val ? 'checked' : '' }}
                   class="sr-only" onchange="toggleMediaInput()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}"/>
            </svg>
            <span class="font-semibold">{{ $meta['label'] }}</span>
            <span class="text-slate-400">{{ $meta['desc'] }}</span>
        </label>
        @endforeach
    </div>
</div>

{{-- Upload Video --}}
<div id="media-video_upload-field">
    <label class="mb-1 block text-xs font-semibold text-slate-700">File Video <span class="text-slate-400">(mp4 / webm, maks 200MB)</span></label>
    <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-600
                  file:mr-3 file:rounded-full file:border-0 file:bg-indigo-50 file:px-3 file:py-1 file:text-xs file:font-medium file:text-indigo-700">
    @if(!empty($section->media_file) && in_array($section->media_type ?? '', ['video_upload', 'upload']))
        <p class="mt-1 text-xs text-slate-400">File saat ini: <span class="text-indigo-600">{{ basename($section->media_file) }}</span></p>
    @endif
</div>

<div id="media-audio_upload-field" class="hidden">
    <label class="mb-1 block text-xs font-semibold text-slate-700">File Audio <span class="text-slate-400">(mp3 / wav / ogg, maks 50MB)</span></label>
    <input type="file" name="audio_file" accept="audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-600
                  file:mr-3 file:rounded-full file:border-0 file:bg-purple-50 file:px-3 file:py-1 file:text-xs file:font-medium file:text-purple-700">
    @if(!empty($section->media_file) && ($section->media_type ?? '') === 'audio_upload')
        <p class="mt-1 text-xs text-slate-400">File saat ini: <span class="text-purple-600">{{ basename($section->media_file) }}</span></p>
    @endif
</div>

<div id="media-youtube-field" class="hidden">
    <label class="mb-1 block text-xs font-semibold text-slate-700">YouTube URL</label>
    <input type="url" name="media_url"
           value="{{ old('media_url', ($section->media_type ?? '') === 'youtube' ? ($section->media_url ?? '') : '') }}"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="https://www.youtube.com/watch?v=...">
</div>

<div id="media-drive-field" class="hidden">
    <label class="mb-1 block text-xs font-semibold text-slate-700">Google Drive URL</label>
    <input type="url" name="media_url"
           value="{{ old('media_url', ($section->media_type ?? '') === 'drive' ? ($section->media_url ?? '') : '') }}"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="https://drive.google.com/file/d/...">
    <div class="mt-1 rounded-xl border border-amber-100 bg-amber-50 px-3 py-2 text-xs text-amber-700">
        <strong>Perhatian:</strong> Pastikan file Drive diset <em>Anyone with the link can view</em>.
    </div>
</div>

{{-- Thumbnail --}}
<div id="thumbnail-field">
    <label class="mb-1 block text-xs font-semibold text-slate-700">Thumbnail <span class="text-slate-400">(jpg/png, maks 2MB)</span></label>
    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-600
                  file:mr-3 file:rounded-full file:border-0 file:bg-slate-100 file:px-3 file:py-1 file:text-xs file:font-medium file:text-slate-600">
    @if(!empty($section->thumbnail_url))
        <img src="{{ $section->thumbnail_url }}" alt="" class="mt-2 h-20 w-full rounded-xl object-cover" loading="lazy">
    @endif
</div>

{{-- Urutan --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Urutan Section <span class="text-red-500">*</span></label>
    <input type="number" name="order" value="{{ old('order', $section->order ?? 1) }}" min="1"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
           required>
</div>

{{-- Published --}}
<label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
    <input type="hidden" name="is_published" value="0">
    <input type="checkbox" name="is_published" value="1"
           {{ old('is_published', $section->is_published ?? false) ? 'checked' : '' }}
           class="h-5 w-5 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
    <div>
        <p class="text-sm font-medium text-slate-800">Publish Section</p>
        <p class="text-xs text-slate-500">Section akan terlihat oleh user jika diaktifkan.</p>
    </div>
</label>

{{-- ═══════════════════ SCRIPTS ═══════════════════ --}}
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
// ─── Single page Quill ────────────────────────────────────────────────────────
const quillSingle = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Tulis materi lengkap di sini...',
    modules: {
        toolbar: [
            [{ header: [2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote', 'code-block'],
            ['clean']
        ]
    }
});

// ─── Multi-page Quill instances (keyed by page index) ────────────────────────
const pageQuills = {};

function initPageQuill(idx) {
    if (pageQuills[idx]) return;
    const el = document.getElementById('page-quill-' + idx);
    if (!el) return;
    pageQuills[idx] = new Quill(el, {
        theme: 'snow',
        placeholder: 'Isi konten page ' + (idx + 1) + '...',
        modules: {
            toolbar: [
                [{ header: [2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote'],
                ['clean']
            ]
        }
    });
    // Seed existing content from hidden input
    const hidden = document.getElementById('page-content-' + idx);
    if (hidden && hidden.value) {
        pageQuills[idx].root.innerHTML = hidden.value;
    }
}

// Init existing pages quill editors on load
document.addEventListener('DOMContentLoaded', function () {
    toggleContentMode();
    toggleMediaInput();
    // Init all existing page quills
    document.querySelectorAll('#pages-container .page-item').forEach(item => {
        const idx = parseInt(item.dataset.pageIndex);
        initPageQuill(idx);
    });
});

// ─── Content mode toggle ──────────────────────────────────────────────────────
function toggleContentMode() {
    const mode = document.querySelector('input[name="content_mode"]:checked')?.value;
    document.getElementById('content-single-wrap')?.classList.toggle('hidden', mode === 'multi');
    document.getElementById('content-multi-wrap')?.classList.toggle('hidden', mode !== 'multi');
}

// ─── Media type toggle ────────────────────────────────────────────────────────
function toggleMediaInput() {
    const type = document.querySelector('input[name="media_type"]:checked')?.value;
    const panels = {
        'video_upload': 'media-video_upload-field',
        'audio_upload': 'media-audio_upload-field',
        'youtube':      'media-youtube-field',
        'drive':        'media-drive-field',
    };
    Object.entries(panels).forEach(([key, id]) => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', key !== type);
    });
}

// ─── Submit: collect all Quill content ───────────────────────────────────────
const form = document.querySelector('form');
if (form) {
    form.addEventListener('submit', function () {
        // Single
        document.getElementById('content-input').value = quillSingle.root.innerHTML;
        // Multi: flush each quill to hidden input
        Object.entries(pageQuills).forEach(([idx, q]) => {
            const hidden = document.getElementById('page-content-' + idx);
            if (hidden) hidden.value = q.root.innerHTML;
        });
        renumberPages();
    });
}

// ─── Page Builder ─────────────────────────────────────────────────────────────
let pageIndex = parseInt(document.getElementById('pages-count')?.value || 0);

function addPage() {
    const container = document.getElementById('pages-container');
    const idx       = pageIndex;
    pageIndex++;
    document.getElementById('pages-count').value = pageIndex;

    const div = document.createElement('div');
    div.className        = 'page-item';
    div.dataset.pageIndex = idx;
    div.innerHTML = `
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-xs font-black text-white page-num">${idx + 1}</span>
            <span class="text-xs font-bold text-slate-600 page-label">Page ${idx + 1}</span>
        </div>
        <button type="button" onclick="removePage(this)" class="text-slate-300 hover:text-red-500 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <input type="text" name="pages[${idx}][title]" placeholder="Judul page (opsional)"
           class="mb-2 block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">
    <div class="mb-2">
        <label class="mb-1 block text-xs text-slate-500">Foto / Gambar <span class="text-slate-400">(opsional)</span></label>
        <div id="img-preview-wrap-${idx}" class="hidden mb-1"><img id="img-preview-${idx}" src="" class="slide-thumb-preview"></div>
        <input type="file" name="pages[${idx}][new_image]" accept="image/jpeg,image/png,image/webp"
               data-preview="img-preview-${idx}" data-preview-wrap="img-preview-wrap-${idx}"
               class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:text-xs"
               onchange="previewPageImage(this)">
    </div>
    <div class="mb-2">
        <label class="mb-1 block text-xs text-slate-500">&#127925; Audio Slide <span class="text-slate-400">(opsional, mp3/wav/ogg maks 50MB)</span></label>
        <input type="file" name="pages[${idx}][new_audio]" accept="audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac"
               class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-purple-50 file:px-2 file:py-1 file:text-xs file:font-medium file:text-purple-700"
               onchange="previewPageAudio(this, ${idx})">
        <div id="audio-new-preview-${idx}" class="hidden mt-1">
            <audio controls class="w-full h-8" style="border-radius:.5rem;"></audio>
            <p class="text-xs text-slate-400 mt-0.5">Preview audio baru</p>
        </div>
    </div>
    <label class="mb-1 block text-xs text-slate-500">Konten <span class="text-red-400">*</span></label>
    <input type="hidden" name="pages[${idx}][content]" id="page-content-${idx}">
    <div id="page-quill-${idx}" style="min-height:120px; background:white;"></div>
    `;
    container.appendChild(div);
    // small delay so DOM settles before Quill init
    setTimeout(() => initPageQuill(idx), 50);
}

function removePage(btn) {
    btn.closest('.page-item').remove();
    renumberPages();
}

function renumberPages() {
    const items = document.querySelectorAll('#pages-container .page-item');
    items.forEach((item, i) => {
        item.querySelectorAll('input[name], textarea[name]').forEach(el => {
            if (el.name) el.name = el.name.replace(/pages\[\d+\]/, `pages[${i}]`);
            if (el.id)   el.id   = el.id.replace(/(page-(?:content|quill)-|img-preview(?:-wrap)?-|audio-(?:new-preview|current)-)\d+$/, `$1${i}`);
        });
        const num   = item.querySelector('.page-num');
        const label = item.querySelector('.page-label');
        if (num)   num.textContent   = i + 1;
        if (label) label.textContent = `Page ${i + 1}`;
        item.dataset.pageIndex = i;
    });
    document.getElementById('pages-count').value = items.length;
    pageIndex = items.length;
}

function previewPageImage(input) {
    const wrap = document.getElementById(input.dataset.previewWrap);
    const img  = document.getElementById(input.dataset.preview);
    if (img && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        if (wrap) wrap.classList.remove('hidden');
    }
}

// ─── Preview audio baru per slide ─────────────────────────────────────────────
function previewPageAudio(input, idx) {
    const wrap  = document.getElementById('audio-new-preview-' + idx);
    if (!wrap) return;
    const audio = wrap.querySelector('audio');
    if (input.files[0]) {
        audio.src = URL.createObjectURL(input.files[0]);
        wrap.classList.remove('hidden');
    } else {
        wrap.classList.add('hidden');
        audio.src = '';
    }
}
</script>
