{{-- Section Form — Multi-Page, 1 slide = banyak media --}}

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
.slide-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.25rem;
    margin-bottom: .75rem;
}
.slide-card .ql-container { min-height: 90px; border-radius: 0 0 .5rem .5rem; }
.slide-card .ql-toolbar  { border-radius: .5rem .5rem 0 0; }

/* Media row */
.slide-media-row {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    padding: .6rem .75rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: .75rem;
    margin-bottom: .5rem;
}
.slide-media-icon {
    flex-shrink: 0;
    width: 28px; height: 28px;
    border-radius: .5rem;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
}
.slide-media-row .media-label {
    font-size: .65rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: .3rem;
    display: block;
}
.slide-media-row .media-hint {
    font-size: .6rem;
    color: #94a3b8;
}
.slide-preview-img { max-height: 110px; width:100%; object-fit:contain; border-radius:.4rem; border:1px solid #e2e8f0; background:#fff; margin-top:.25rem; }
.slide-preview-video { max-height: 120px; width:100%; border-radius:.4rem; background:#000; margin-top:.25rem; }
.slide-preview-audio { width:100%; margin-top:.25rem; }

/* Toggle collapse */
.media-toggle-btn {
    display: flex;
    align-items: center;
    gap: .4rem;
    padding: .25rem .6rem;
    font-size: .62rem;
    font-weight: 700;
    border-radius: 9999px;
    border: 1px solid #e2e8f0;
    background: #f1f5f9;
    color: #64748b;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.media-toggle-btn.active {
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
            <p class="text-xs text-slate-400">1 slide bisa memuat gambar, video, audio, YouTube, Drive, dan teks sekaligus.</p>
        </div>
        <button type="button" onclick="addSlide()"
                class="flex items-center gap-1.5 rounded-full bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm active:scale-95 transition">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Slide
        </button>
    </div>

    @php $existingPages = (!empty($section->pages) && is_array($section->pages)) ? $section->pages : []; @endphp

    <div id="slides-container" class="space-y-3">
        @forelse($existingPages as $pi => $page)
        <div class="slide-card" data-slide-index="{{ $pi }}">

            {{-- Header --}}
            <div class="mb-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 text-xs font-black text-white slide-num">{{ $pi+1 }}</span>
                    <span class="text-xs font-bold text-slate-600 slide-label">Slide {{ $pi+1 }}</span>
                </div>
                <button type="button" onclick="removeSlide(this)" class="text-slate-300 hover:text-red-500 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Judul Slide --}}
            <input type="text" name="pages[{{ $pi }}][title]" value="{{ $page['title'] ?? '' }}"
                   placeholder="Judul slide (opsional)"
                   class="mb-3 block w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-indigo-400">

            {{-- ── Toggle buttons — tampilkan/sembunyikan tiap media ── --}}
            <div class="mb-3">
                <p class="mb-1.5 text-xs font-semibold text-slate-600">Aktifkan Media di Slide Ini</p>
                <div class="flex flex-wrap gap-1.5">
                    @php
                        $hasImg   = !empty($page['image_url'])  || !empty($page['image_path']);
                        $hasVid   = !empty($page['video_url'])  || !empty($page['video_path']);
                        $hasAud   = !empty($page['audio_url'])  || !empty($page['audio_path']);
                        $hasYt    = !empty($page['youtube_url']);
                        $hasDrv   = !empty($page['drive_url']);
                    @endphp
                    <button type="button" onclick="toggleMedia(this,'img-row-{{ $pi }}')"
                            class="media-toggle-btn {{ $hasImg ? 'active' : '' }}" data-active="{{ $hasImg ? '1':'0' }}">🖼 Gambar</button>
                    <button type="button" onclick="toggleMedia(this,'vid-row-{{ $pi }}')"
                            class="media-toggle-btn {{ $hasVid ? 'active' : '' }}" data-active="{{ $hasVid ? '1':'0' }}">🎬 Video</button>
                    <button type="button" onclick="toggleMedia(this,'aud-row-{{ $pi }}')"
                            class="media-toggle-btn {{ $hasAud ? 'active' : '' }}" data-active="{{ $hasAud ? '1':'0' }}">🎵 Audio</button>
                    <button type="button" onclick="toggleMedia(this,'yt-row-{{ $pi }}')"
                            class="media-toggle-btn {{ $hasYt ? 'active' : '' }}" data-active="{{ $hasYt ? '1':'0' }}">▶ YouTube</button>
                    <button type="button" onclick="toggleMedia(this,'drv-row-{{ $pi }}')"
                            class="media-toggle-btn {{ $hasDrv ? 'active' : '' }}" data-active="{{ $hasDrv ? '1':'0' }}">☁ Drive</button>
                </div>
            </div>

            {{-- ── Media: Gambar ── --}}
            <div id="img-row-{{ $pi }}" class="slide-media-row" {{ !$hasImg ? 'style=display:none' : '' }}>
                <div class="slide-media-icon" style="background:#f0fdf4">🖼</div>
                <div class="flex-1">
                    <span class="media-label">Gambar <span class="media-hint">(jpg/png/webp, maks 5MB)</span></span>
                    @if(!empty($page['image_url']))
                        <img src="{{ $page['image_url'] }}" class="slide-preview-img mb-1" loading="lazy">
                    @endif
                    <input type="hidden" name="pages[{{ $pi }}][image_url]"  value="{{ $page['image_url']  ?? '' }}">
                    <input type="hidden" name="pages[{{ $pi }}][image_path]" value="{{ $page['image_path'] ?? '' }}">
                    <input type="file" name="pages[{{ $pi }}][new_image]" accept="image/jpeg,image/png,image/webp"
                           class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:text-xs"
                           onchange="previewSlideImage(this)">
                </div>
            </div>

            {{-- ── Media: Video ── --}}
            <div id="vid-row-{{ $pi }}" class="slide-media-row" {{ !$hasVid ? 'style=display:none' : '' }}>
                <div class="slide-media-icon" style="background:#eff6ff">🎬</div>
                <div class="flex-1">
                    <span class="media-label">Video <span class="media-hint">(mp4/webm/mov, maks 200MB)</span></span>
                    @if(!empty($page['video_url']))
                        <video src="{{ $page['video_url'] }}" controls class="slide-preview-video mb-1"></video>
                    @endif
                    <input type="hidden" name="pages[{{ $pi }}][video_url]"  value="{{ $page['video_url']  ?? '' }}">
                    <input type="hidden" name="pages[{{ $pi }}][video_path]" value="{{ $page['video_path'] ?? '' }}">
                    <input type="file" name="pages[{{ $pi }}][new_video]" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo"
                           class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-indigo-50 file:px-2 file:py-1 file:text-xs file:text-indigo-700"
                           onchange="previewSlideVideo(this)">
                </div>
            </div>

            {{-- ── Media: Audio ── --}}
            <div id="aud-row-{{ $pi }}" class="slide-media-row" {{ !$hasAud ? 'style=display:none' : '' }}>
                <div class="slide-media-icon" style="background:#fdf4ff">🎵</div>
                <div class="flex-1">
                    <span class="media-label">Audio <span class="media-hint">(mp3/wav/ogg/aac, maks 50MB)</span></span>
                    @if(!empty($page['audio_url']))
                        <audio src="{{ $page['audio_url'] }}" controls class="slide-preview-audio mb-1"></audio>
                    @endif
                    <input type="hidden" name="pages[{{ $pi }}][audio_url]"  value="{{ $page['audio_url']  ?? '' }}">
                    <input type="hidden" name="pages[{{ $pi }}][audio_path]" value="{{ $page['audio_path'] ?? '' }}">
                    <input type="file" name="pages[{{ $pi }}][new_audio]" accept="audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac"
                           class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-purple-50 file:px-2 file:py-1 file:text-xs file:text-purple-700"
                           onchange="previewSlideAudio(this)">
                </div>
            </div>

            {{-- ── Media: YouTube ── --}}
            <div id="yt-row-{{ $pi }}" class="slide-media-row" {{ !$hasYt ? 'style=display:none' : '' }}>
                <div class="slide-media-icon" style="background:#fff1f2">▶</div>
                <div class="flex-1">
                    <span class="media-label">YouTube URL</span>
                    <input type="url" name="pages[{{ $pi }}][youtube_url]" value="{{ $page['youtube_url'] ?? '' }}"
                           placeholder="https://www.youtube.com/watch?v=..."
                           class="block w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-indigo-400">
                </div>
            </div>

            {{-- ── Media: Drive ── --}}
            <div id="drv-row-{{ $pi }}" class="slide-media-row" {{ !$hasDrv ? 'style=display:none' : '' }}>
                <div class="slide-media-icon" style="background:#fefce8">☁</div>
                <div class="flex-1">
                    <span class="media-label">Google Drive URL</span>
                    <input type="url" name="pages[{{ $pi }}][drive_url]" value="{{ $page['drive_url'] ?? '' }}"
                           placeholder="https://drive.google.com/file/d/..."
                           class="block w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-indigo-400">
                    <span class="media-hint text-amber-500">Pastikan sharing diset: Anyone with the link</span>
                </div>
            </div>

            {{-- ── Konten Teks ── --}}
            <div class="mt-3">
                <label class="mb-1 block text-xs font-semibold text-slate-600">Teks / Deskripsi Slide <span class="text-slate-400">(opsional)</span></label>
                <input type="hidden" name="pages[{{ $pi }}][content]" id="page-content-{{ $pi }}" value="{{ $page['content'] ?? '' }}">
                <div id="page-quill-{{ $pi }}" class="border border-slate-200" style="min-height:90px;background:#fff;border-radius:.5rem;">{!! $page['content'] ?? '' !!}</div>
            </div>
        </div>
        @empty
        <div id="slides-empty" class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center">
            <p class="text-xs text-slate-400">Belum ada slide. Klik <strong>Tambah Slide</strong> untuk mulai.</p>
        </div>
        @endforelse
    </div>

    <input type="hidden" name="pages_count" id="slides-count" value="{{ count($existingPages) }}">
</div>

{{-- Thumbnail Section --}}
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
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
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
const pageQuills = {};

function initSlideQuill(idx) {
    if (pageQuills[idx]) return;
    const el = document.getElementById('page-quill-' + idx);
    if (!el) return;
    pageQuills[idx] = new Quill(el, {
        theme: 'snow',
        placeholder: 'Isi teks slide ' + (idx + 1) + '...',
        modules: {
            toolbar: [
                [{ header: [2, 3, false] }],
                ['bold','italic','underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote'],['clean']
            ]
        }
    });
    const hidden = document.getElementById('page-content-' + idx);
    if (hidden && hidden.value) pageQuills[idx].root.innerHTML = hidden.value;
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#slides-container .slide-card').forEach(c => initSlideQuill(+c.dataset.slideIndex));
});

const _form = document.querySelector('form');
if (_form) {
    _form.addEventListener('submit', () => {
        Object.entries(pageQuills).forEach(([idx, q]) => {
            const h = document.getElementById('page-content-' + idx);
            if (h) h.value = q.root.innerHTML;
        });
        renumberSlides();
    });
}

// ─── slide counter ───────────────────────────────────────────────────
let slideIndex = +(document.getElementById('slides-count')?.value || 0);

function addSlide() {
    document.getElementById('slides-empty')?.remove();
    const container = document.getElementById('slides-container');
    const idx = slideIndex++;
    document.getElementById('slides-count').value = slideIndex;
    const div = document.createElement('div');
    div.className = 'slide-card';
    div.dataset.slideIndex = idx;
    div.innerHTML = buildSlideHTML(idx);
    container.appendChild(div);
    setTimeout(() => initSlideQuill(idx), 60);
}

function buildSlideHTML(idx) {
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

    {{-- Toggle buttons --}}
    <div class="mb-3">
        <p class="mb-1.5 text-xs font-semibold text-slate-600">Aktifkan Media di Slide Ini</p>
        <div class="flex flex-wrap gap-1.5">
            <button type="button" onclick="toggleMedia(this,'img-row-${idx}')" class="media-toggle-btn" data-active="0">&#128444; Gambar</button>
            <button type="button" onclick="toggleMedia(this,'vid-row-${idx}')" class="media-toggle-btn" data-active="0">&#127916; Video</button>
            <button type="button" onclick="toggleMedia(this,'aud-row-${idx}')" class="media-toggle-btn" data-active="0">&#127925; Audio</button>
            <button type="button" onclick="toggleMedia(this,'yt-row-${idx}')"  class="media-toggle-btn" data-active="0">&#9654; YouTube</button>
            <button type="button" onclick="toggleMedia(this,'drv-row-${idx}')" class="media-toggle-btn" data-active="0">&#9729; Drive</button>
        </div>
    </div>

    {{-- Gambar --}}
    <div id="img-row-${idx}" class="slide-media-row" style="display:none">
        <div class="slide-media-icon" style="background:#f0fdf4">&#128444;</div>
        <div class="flex-1">
            <span class="media-label">Gambar <span class="media-hint">(jpg/png/webp, maks 5MB)</span></span>
            <input type="file" name="pages[${idx}][new_image]" accept="image/jpeg,image/png,image/webp"
                   class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:text-xs"
                   onchange="previewSlideImage(this)">
            <div style="display:none" data-img-preview><img class="slide-preview-img"></div>
        </div>
    </div>

    {{-- Video --}}
    <div id="vid-row-${idx}" class="slide-media-row" style="display:none">
        <div class="slide-media-icon" style="background:#eff6ff">&#127916;</div>
        <div class="flex-1">
            <span class="media-label">Video <span class="media-hint">(mp4/webm/mov, maks 200MB)</span></span>
            <input type="file" name="pages[${idx}][new_video]" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo"
                   class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-indigo-50 file:px-2 file:py-1 file:text-xs file:text-indigo-700"
                   onchange="previewSlideVideo(this)">
            <div style="display:none" data-video-preview><video controls class="slide-preview-video"></video></div>
        </div>
    </div>

    {{-- Audio --}}
    <div id="aud-row-${idx}" class="slide-media-row" style="display:none">
        <div class="slide-media-icon" style="background:#fdf4ff">&#127925;</div>
        <div class="flex-1">
            <span class="media-label">Audio <span class="media-hint">(mp3/wav/ogg/aac, maks 50MB)</span></span>
            <input type="file" name="pages[${idx}][new_audio]" accept="audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/aac"
                   class="block w-full text-xs text-slate-500 file:rounded-full file:border-0 file:bg-purple-50 file:px-2 file:py-1 file:text-xs file:text-purple-700"
                   onchange="previewSlideAudio(this)">
            <div style="display:none" data-audio-preview><audio controls class="slide-preview-audio"></audio></div>
        </div>
    </div>

    {{-- YouTube --}}
    <div id="yt-row-${idx}" class="slide-media-row" style="display:none">
        <div class="slide-media-icon" style="background:#fff1f2">&#9654;</div>
        <div class="flex-1">
            <span class="media-label">YouTube URL</span>
            <input type="url" name="pages[${idx}][youtube_url]" placeholder="https://www.youtube.com/watch?v=..."
                   class="block w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-indigo-400">
        </div>
    </div>

    {{-- Drive --}}
    <div id="drv-row-${idx}" class="slide-media-row" style="display:none">
        <div class="slide-media-icon" style="background:#fefce8">&#9729;</div>
        <div class="flex-1">
            <span class="media-label">Google Drive URL</span>
            <input type="url" name="pages[${idx}][drive_url]" placeholder="https://drive.google.com/file/d/..."
                   class="block w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs focus:border-indigo-400">
            <span class="media-hint text-amber-500">Pastikan sharing: Anyone with the link</span>
        </div>
    </div>

    {{-- Teks --}}
    <div class="mt-3">
        <label class="mb-1 block text-xs font-semibold text-slate-600">Teks / Deskripsi Slide <span class="text-slate-400">(opsional)</span></label>
        <input type="hidden" name="pages[${idx}][content]" id="page-content-${idx}">
        <div id="page-quill-${idx}" class="border border-slate-200" style="min-height:90px;background:#fff;border-radius:.5rem;"></div>
    </div>`;
}

function removeSlide(btn) {
    btn.closest('.slide-card').remove();
    renumberSlides();
    if (!document.querySelectorAll('#slides-container .slide-card').length) {
        const e = document.createElement('div');
        e.id = 'slides-empty';
        e.className = 'rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center';
        e.innerHTML = '<p class="text-xs text-slate-400">Belum ada slide. Klik <strong>Tambah Slide</strong> untuk mulai.</p>';
        document.getElementById('slides-container').appendChild(e);
    }
}

function renumberSlides() {
    const cards = document.querySelectorAll('#slides-container .slide-card');
    cards.forEach((card, i) => {
        card.dataset.slideIndex = i;
        card.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/pages\[\d+\]/, `pages[${i}]`);
        });
        card.querySelectorAll('[id]').forEach(el => {
            el.id = el.id.replace(/((?:img|vid|aud|yt|drv)-row-|page-(?:content|quill)-)(\d+)$/, `$1${i}`);
        });
        card.querySelector('.slide-num').textContent = i + 1;
        card.querySelector('.slide-label').textContent = `Slide ${i + 1}`;
    });
    document.getElementById('slides-count').value = cards.length;
    slideIndex = cards.length;
}

// Toggle show/hide media row
function toggleMedia(btn, rowId) {
    const row = document.getElementById(rowId);
    if (!row) return;
    const isActive = btn.dataset.active === '1';
    if (isActive) {
        row.style.display = 'none';
        btn.classList.remove('active');
        btn.dataset.active = '0';
    } else {
        row.style.display = 'flex';
        btn.classList.add('active');
        btn.dataset.active = '1';
    }
}

// Preview helpers
function previewSlideImage(input) {
    const row  = input.closest('.slide-media-row');
    const wrap = row.querySelector('[data-img-preview]');
    const img  = wrap?.querySelector('img');
    if (img && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        wrap.style.display = '';
    }
}
function previewSlideVideo(input) {
    const row   = input.closest('.slide-media-row');
    const wrap  = row.querySelector('[data-video-preview]');
    const video = wrap?.querySelector('video');
    if (video && input.files[0]) {
        video.src = URL.createObjectURL(input.files[0]);
        wrap.style.display = '';
    }
}
function previewSlideAudio(input) {
    const row   = input.closest('.slide-media-row');
    const wrap  = row.querySelector('[data-audio-preview]');
    const audio = wrap?.querySelector('audio');
    if (audio && input.files[0]) {
        audio.src = URL.createObjectURL(input.files[0]);
        wrap.style.display = '';
    }
}
</script>
