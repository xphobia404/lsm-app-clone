{{-- Section Form — Multi-Page Only (single page removed) --}}

{{-- Quill CSS --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
.slide-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1rem;
    margin-bottom: .75rem;
}
.slide-card .ql-container { min-height: 100px; border-radius: 0 0 .5rem .5rem; }
.slide-card .ql-toolbar  { border-radius: .5rem .5rem 0 0; }
.slide-thumb-preview {
    width: 100%; max-height: 140px;
    object-fit: contain;
    border-radius: .5rem;
    border: 1px solid #e2e8f0;
    margin-top: .25rem;
    background: #fff;
}
.media-tab-btn {
    flex: 1;
    padding: .3rem .5rem;
    font-size: .65rem;
    font-weight: 600;
    border-radius: 9999px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    cursor: pointer;
    transition: all .15s;
    text-align: center;
    white-space: nowrap;
}
.media-tab-btn.active {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
}
</style>

@if($errors->any())
<x-alert type="error" class="mb-2">
    <ul class="list-disc list-inside text-xs space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</x-alert>
@endif

{{-- Hidden: mode selalu multi --}}
<input type="hidden" name="content_mode" value="multi">

{{-- Spesialisasi Course --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Spesialisasi Course</label>
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

{{-- ═══ SLIDE BUILDER ═══ --}}
<div>
    <div class="mb-3 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-700">Slide Builder</p>
            <p class="text-xs text-slate-400">Setiap slide bisa punya teks, gambar, video, audio, YouTube, atau Drive.</p>
        </div>
        <button type="button" onclick="addSlide()"
                class="flex items-center gap-1.5 rounded-full bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm active:scale-95 transition">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Slide
        </button>
    </div>

    @php
        $existingPages = (!empty($section->pages) && is_array($section->pages)) ? $section->pages : [];
    @endphp

    <div id="slides-container" class="space-y-3">
        @forelse($existingPages as $pi => $page)
        @php
            $slideMedia = $page['slide_media_type'] ?? 'none';
        @endphp
        <div class="slide-card" data-slide-index="{{ $pi }}">

            {{-- ── Header slide ── --}}
            <div class="mb-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-xs font-black text-white slide-num">{{ $pi + 1 }}</span>
                    <span class="text-xs font-bold text-slate-600 slide-label">Slide {{ $pi + 1 }}</span>
                </div>
                <button type="button" onclick="removeSlide(this)" class="text-slate-300 hover:text-red-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- ── Judul slide ── --}}
            <input type="text" name="pages[{{ $pi }}][title]" value="{{ $page['title'] ?? '' }}"
                   placeholder="Judul slide (opsional)"
                   class="mb-3 block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">

            {{-- ── Media Type Tabs ── --}}
            <div class="mb-3">
                <p class="mb-1.5 text-xs font-semibold text-slate-600">Tipe Media Slide</p>
                <div class="flex gap-1.5 flex-wrap" role="group">
                    @foreach(['none'=>'Teks Saja','image'=>'🖼 Gambar','video_upload'=>'🎬 Video','audio'=>'🎵 Audio','youtube'=>'▶ YouTube','drive'=>'☁ Drive'] as $mval => $mlabel)
                    <button type="button"
                            class="media-tab-btn {{ $slideMedia === $mval ? 'active' : '' }}"
                            onclick="switchSlideMedia(this, '{{ $mval }}', {{ $pi }})"
                            data-mtype="{{ $mval }}">
                        {{ $mlabel }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="pages[{{ $pi }}][slide_media_type]" value="{{ $slideMedia }}" class="slide-media-type-input">
            </div>

            {{-- ── Panel: Gambar ── --}}
            <div class="slide-media-panel" data-panel="image" {{ $slideMedia !== 'image' ? 'style=display:none' : '' }}>
                <label class="mb-1 block text-xs text-slate-500">Upload Gambar <span class="text-slate-400">(jpg/png/webp, maks 5MB)</span></label>
                @if(!empty($page['image_url']))
                    <img src="{{ $page['image_url'] }}" class="slide-thumb-preview mb-1" loading="lazy">
                @endif
                <input type="hidden" name="pages[{{ $pi }}][image_url]" value="{{ $page['image_url'] ?? '' }}">
                <input type="hidden" name="pages[{{ $pi }}][image_path]" value="{{ $page['image_path'] ?? '' }}">
                <input type="file" name="pages[{{ $pi }}][new_image]" accept="image/jpeg,image/png,image/webp"
                       class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:text-xs"
                       onchange="previewSlideImage(this)">
            </div>

            {{-- ── Panel: Video Upload ── --}}
            <div class="slide-media-panel" data-panel="video_upload" {{ $slideMedia !== 'video_upload' ? 'style=display:none' : '' }}>
                <label class="mb-1 block text-xs text-slate-500">Upload Video <span class="text-slate-400">(mp4/webm/mov, maks 200MB)</span></label>
                @if(!empty($page['video_url']))
                    <video src="{{ $page['video_url'] }}" controls class="w-full max-h-36 rounded-xl mb-1 bg-black"></video>
                @endif
                <input type="hidden" name="pages[{{ $pi }}][video_url]" value="{{ $page['video_url'] ?? '' }}">
                <input type="hidden" name="pages[{{ $pi }}][video_path]" value="{{ $page['video_path'] ?? '' }}">
                <input type="file" name="pages[{{ $pi }}][new_video]" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo"
                       class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-indigo-50 file:px-2 file:py-1 file:text-xs file:text-indigo-700"
                       onchange="previewSlideVideo(this)">
            </div>

            {{-- ── Panel: Audio ── --}}
            <div class="slide-media-panel" data-panel="audio" {{ $slideMedia !== 'audio' ? 'style=display:none' : '' }}>
                <label class="mb-1 block text-xs text-slate-500">Upload Audio <span class="text-slate-400">(mp3/wav/ogg/aac, maks 50MB)</span></label>
                @if(!empty($page['audio_url']))
                    <audio src="{{ $page['audio_url'] }}" controls class="w-full mb-1"></audio>
                @endif
                <input type="hidden" name="pages[{{ $pi }}][audio_url]" value="{{ $page['audio_url'] ?? '' }}">
                <input type="hidden" name="pages[{{ $pi }}][audio_path]" value="{{ $page['audio_path'] ?? '' }}">
                <input type="file" name="pages[{{ $pi }}][new_audio]" accept="audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac"
                       class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-purple-50 file:px-2 file:py-1 file:text-xs file:text-purple-700"
                       onchange="previewSlideAudio(this)">
            </div>

            {{-- ── Panel: YouTube ── --}}
            <div class="slide-media-panel" data-panel="youtube" {{ $slideMedia !== 'youtube' ? 'style=display:none' : '' }}>
                <label class="mb-1 block text-xs text-slate-500">YouTube URL</label>
                <input type="url" name="pages[{{ $pi }}][youtube_url]" value="{{ $page['youtube_url'] ?? '' }}"
                       placeholder="https://www.youtube.com/watch?v=..."
                       class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">
            </div>

            {{-- ── Panel: Drive ── --}}
            <div class="slide-media-panel" data-panel="drive" {{ $slideMedia !== 'drive' ? 'style=display:none' : '' }}>
                <label class="mb-1 block text-xs text-slate-500">Google Drive URL</label>
                <input type="url" name="pages[{{ $pi }}][drive_url]" value="{{ $page['drive_url'] ?? '' }}"
                       placeholder="https://drive.google.com/file/d/..."
                       class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">
                <p class="mt-1 text-xs text-amber-600">Pastikan file diset <em>Anyone with the link can view</em>.</p>
            </div>

            {{-- ── Konten Teks ── --}}
            <div class="mt-3">
                <label class="mb-1 block text-xs text-slate-500">Konten Teks Slide <span class="text-slate-400">(opsional)</span></label>
                <input type="hidden" name="pages[{{ $pi }}][content]" id="page-content-{{ $pi }}" value="{{ $page['content'] ?? '' }}">
                <div id="page-quill-{{ $pi }}" class="border border-slate-200" style="min-height:100px; background:white; border-radius:.5rem;">{!! $page['content'] ?? '' !!}</div>
            </div>
        </div>
        @empty
        {{-- Placeholder jika belum ada slide --}}
        <div id="slides-empty" class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center">
            <p class="text-xs text-slate-400">Belum ada slide. Klik <strong>Tambah Slide</strong> untuk mulai.</p>
        </div>
        @endforelse
    </div>

    <input type="hidden" name="pages_count" id="slides-count" value="{{ count($existingPages) }}">
</div>

{{-- Thumbnail Section (cover) --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Thumbnail Section <span class="text-slate-400">(jpg/png, maks 2MB)</span></label>
    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-600
                  file:mr-3 file:rounded-full file:border-0 file:bg-slate-100 file:px-3 file:py-1 file:text-xs file:font-medium file:text-slate-600">
    @if(!empty($section->thumbnail) && !empty($section->getThumbnailUrl()))
        <img src="{{ $section->getThumbnailUrl() }}" alt="" class="mt-2 h-20 w-full rounded-xl object-cover" loading="lazy">
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
        <p class="text-xs text-slate-500">Section terlihat oleh user jika diaktifkan.</p>
    </div>
</label>

{{-- ═══ SCRIPTS ═══ --}}
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
// ─── Quill instances per slide ──────────────────────────────────────────────
const pageQuills = {};

function initSlideQuill(idx) {
    if (pageQuills[idx]) return;
    const el = document.getElementById('page-quill-' + idx);
    if (!el) return;
    pageQuills[idx] = new Quill(el, {
        theme: 'snow',
        placeholder: 'Isi konten slide ' + (idx + 1) + '...',
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
    const hidden = document.getElementById('page-content-' + idx);
    if (hidden && hidden.value) {
        pageQuills[idx].root.innerHTML = hidden.value;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#slides-container .slide-card').forEach(card => {
        initSlideQuill(parseInt(card.dataset.slideIndex));
    });
});

// Flush quill content to hidden inputs on submit
const _form = document.querySelector('form');
if (_form) {
    _form.addEventListener('submit', () => {
        Object.entries(pageQuills).forEach(([idx, q]) => {
            const hidden = document.getElementById('page-content-' + idx);
            if (hidden) hidden.value = q.root.innerHTML;
        });
        renumberSlides();
    });
}

// ─── Slide Builder ──────────────────────────────────────────────────────────
let slideIndex = parseInt(document.getElementById('slides-count')?.value || 0);

function addSlide() {
    document.getElementById('slides-empty')?.remove();
    const container = document.getElementById('slides-container');
    const idx = slideIndex++;
    document.getElementById('slides-count').value = slideIndex;

    const div = document.createElement('div');
    div.className = 'slide-card';
    div.dataset.slideIndex = idx;
    div.innerHTML = slideTemplate(idx);
    container.appendChild(div);
    setTimeout(() => initSlideQuill(idx), 60);
}

function slideTemplate(idx) {
    const mediaTypes = [
        { val: 'none',         label: 'Teks Saja' },
        { val: 'image',        label: '🖼 Gambar' },
        { val: 'video_upload', label: '🎬 Video' },
        { val: 'audio',        label: '🎵 Audio' },
        { val: 'youtube',      label: '▶ YouTube' },
        { val: 'drive',        label: '☁ Drive' },
    ];
    const tabsHtml = mediaTypes.map(m =>
        `<button type="button" class="media-tab-btn${m.val==='none'?' active':''}" onclick="switchSlideMedia(this,'${m.val}',${idx})" data-mtype="${m.val}">${m.label}</button>`
    ).join('');

    return `
    <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-xs font-black text-white slide-num">${idx+1}</span>
            <span class="text-xs font-bold text-slate-600 slide-label">Slide ${idx+1}</span>
        </div>
        <button type="button" onclick="removeSlide(this)" class="text-slate-300 hover:text-red-500 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <input type="text" name="pages[${idx}][title]" placeholder="Judul slide (opsional)"
           class="mb-3 block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">
    <div class="mb-3">
        <p class="mb-1.5 text-xs font-semibold text-slate-600">Tipe Media Slide</p>
        <div class="flex gap-1.5 flex-wrap">${tabsHtml}</div>
        <input type="hidden" name="pages[${idx}][slide_media_type]" value="none" class="slide-media-type-input">
    </div>
    <div class="slide-media-panel" data-panel="image" style="display:none">
        <label class="mb-1 block text-xs text-slate-500">Upload Gambar <span class="text-slate-400">(jpg/png/webp, maks 5MB)</span></label>
        <input type="file" name="pages[${idx}][new_image]" accept="image/jpeg,image/png,image/webp"
               class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:text-xs"
               onchange="previewSlideImage(this)">
        <div class="hidden mt-1" data-img-preview><img class="slide-thumb-preview"></div>
    </div>
    <div class="slide-media-panel" data-panel="video_upload" style="display:none">
        <label class="mb-1 block text-xs text-slate-500">Upload Video <span class="text-slate-400">(mp4/webm/mov, maks 200MB)</span></label>
        <input type="file" name="pages[${idx}][new_video]" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo"
               class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-indigo-50 file:px-2 file:py-1 file:text-xs file:text-indigo-700"
               onchange="previewSlideVideo(this)">
        <div class="hidden mt-1" data-video-preview><video controls class="w-full max-h-36 rounded-xl bg-black"></video></div>
    </div>
    <div class="slide-media-panel" data-panel="audio" style="display:none">
        <label class="mb-1 block text-xs text-slate-500">Upload Audio <span class="text-slate-400">(mp3/wav/ogg, maks 50MB)</span></label>
        <input type="file" name="pages[${idx}][new_audio]" accept="audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac"
               class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-purple-50 file:px-2 file:py-1 file:text-xs file:text-purple-700"
               onchange="previewSlideAudio(this)">
        <div class="hidden mt-1" data-audio-preview><audio controls class="w-full"></audio></div>
    </div>
    <div class="slide-media-panel" data-panel="youtube" style="display:none">
        <label class="mb-1 block text-xs text-slate-500">YouTube URL</label>
        <input type="url" name="pages[${idx}][youtube_url]" placeholder="https://www.youtube.com/watch?v=..."
               class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">
    </div>
    <div class="slide-media-panel" data-panel="drive" style="display:none">
        <label class="mb-1 block text-xs text-slate-500">Google Drive URL</label>
        <input type="url" name="pages[${idx}][drive_url]" placeholder="https://drive.google.com/file/d/..."
               class="block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">
        <p class="mt-1 text-xs text-amber-600">Pastikan file diset <em>Anyone with the link can view</em>.</p>
    </div>
    <div class="mt-3">
        <label class="mb-1 block text-xs text-slate-500">Konten Teks Slide <span class="text-slate-400">(opsional)</span></label>
        <input type="hidden" name="pages[${idx}][content]" id="page-content-${idx}">
        <div id="page-quill-${idx}" class="border border-slate-200" style="min-height:100px;background:white;border-radius:.5rem;"></div>
    </div>`;
}

function removeSlide(btn) {
    btn.closest('.slide-card').remove();
    renumberSlides();
    if (!document.querySelectorAll('#slides-container .slide-card').length) {
        const empty = document.createElement('div');
        empty.id = 'slides-empty';
        empty.className = 'rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center';
        empty.innerHTML = '<p class="text-xs text-slate-400">Belum ada slide. Klik <strong>Tambah Slide</strong> untuk mulai.</p>';
        document.getElementById('slides-container').appendChild(empty);
    }
}

function renumberSlides() {
    const cards = document.querySelectorAll('#slides-container .slide-card');
    cards.forEach((card, i) => {
        card.dataset.slideIndex = i;
        // Rename all input names
        card.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/pages\[\d+\]/, `pages[${i}]`);
        });
        // Rename IDs
        card.querySelectorAll('[id]').forEach(el => {
            el.id = el.id.replace(/(page-(?:content|quill)-)\d+$/, `$1${i}`);
        });
        const num = card.querySelector('.slide-num');
        const lbl = card.querySelector('.slide-label');
        if (num) num.textContent = i + 1;
        if (lbl) lbl.textContent = `Slide ${i + 1}`;
    });
    document.getElementById('slides-count').value = cards.length;
    slideIndex = cards.length;
}

// ─── Media Tab Switcher per slide ──────────────────────────────────────────
function switchSlideMedia(btn, type, idx) {
    const card = btn.closest('.slide-card');
    // Update active tab
    card.querySelectorAll('.media-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    // Update hidden input
    card.querySelector('.slide-media-type-input').value = type;
    // Show/hide panels
    card.querySelectorAll('.slide-media-panel').forEach(panel => {
        panel.style.display = panel.dataset.panel === type ? '' : 'none';
    });
}

// ─── Preview Helpers ───────────────────────────────────────────────────────
function previewSlideImage(input) {
    const card = input.closest('.slide-card');
    const wrap = card.querySelector('[data-img-preview]');
    const img  = wrap?.querySelector('img');
    if (img && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        wrap.classList.remove('hidden');
    }
}

function previewSlideVideo(input) {
    const card  = input.closest('.slide-card');
    const wrap  = card.querySelector('[data-video-preview]');
    const video = wrap?.querySelector('video');
    if (video && input.files[0]) {
        video.src = URL.createObjectURL(input.files[0]);
        wrap.classList.remove('hidden');
    }
}

function previewSlideAudio(input) {
    const card  = input.closest('.slide-card');
    const wrap  = card.querySelector('[data-audio-preview]');
    const audio = wrap?.querySelector('audio');
    if (audio && input.files[0]) {
        audio.src = URL.createObjectURL(input.files[0]);
        wrap.classList.remove('hidden');
    }
}
</script>
