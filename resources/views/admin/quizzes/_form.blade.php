{{-- Validation errors --}}
@if($errors->any())
<div class="mb-1 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
    <p class="mb-1.5 text-xs font-semibold text-red-600">Terdapat kesalahan input:</p>
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $e)
        <li class="text-xs text-red-500">{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

@php
    $isActiveVal = old('is_active', $quiz ? (int) $quiz->is_active : 1);
@endphp

{{-- Pertanyaan --}}
<div class="space-y-1">
    <label for="question" class="block text-xs font-semibold text-slate-700">
        Pertanyaan <span class="text-red-500">*</span>
    </label>
    <textarea id="question" name="question" rows="3" maxlength="1000" required
              placeholder="Tulis pertanyaan di sini..."
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm
                     placeholder-slate-300 resize-none transition
                     focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200
                     @error('question') border-red-400 bg-red-50 @enderror"
    >{{ old('question', $quiz?->question ?? '') }}</textarea>
    @error('question')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Opsi Jawaban --}}
<div class="space-y-2">
    <label class="block text-xs font-semibold text-slate-700">Opsi Jawaban</label>
    <div class="space-y-2">
        @foreach(['a','b','c','d'] as $opt)
        @php $required = in_array($opt, ['a','b']); @endphp
        <div class="flex items-center gap-2">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                         bg-indigo-100 text-xs font-bold text-indigo-700 uppercase">{{ $opt }}</span>
            <input type="text" name="option_{{ $opt }}"
                   value="{{ old('option_'.$opt, $quiz?->{'option_'.$opt} ?? '') }}"
                   placeholder="Opsi {{ strtoupper($opt) }}{{ $required ? '' : ' (opsional)' }}"
                   {{ $required ? 'required' : '' }} maxlength="255"
                   class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm
                          placeholder-slate-300 transition
                          focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200
                          @error('option_'.$opt) border-red-400 bg-red-50 @enderror">
        </div>
        @error('option_'.$opt)<p class="ml-9 text-xs text-red-500">{{ $message }}</p>@enderror
        @endforeach
    </div>
</div>

{{-- Jawaban Benar --}}
<div class="space-y-1">
    <label class="block text-xs font-semibold text-slate-700">Jawaban Benar <span class="text-red-500">*</span></label>
    <div class="flex gap-3">
        @foreach(['a','b','c','d'] as $opt)
        <label class="flex items-center gap-1.5 cursor-pointer">
            <input type="radio" name="correct_answer" value="{{ $opt }}"
                   {{ old('correct_answer', $quiz?->correct_answer) === $opt ? 'checked' : '' }}
                   class="text-indigo-600 focus:ring-indigo-300">
            <span class="text-sm font-semibold text-slate-700 uppercase">{{ $opt }}</span>
        </label>
        @endforeach
    </div>
    @error('correct_answer')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Penjelasan --}}
<div class="space-y-1">
    <label for="explanation" class="block text-xs font-semibold text-slate-700">
        Penjelasan
        <span class="font-normal text-slate-400">(opsional — ditampilkan setelah menjawab)</span>
    </label>
    <textarea id="explanation" name="explanation" rows="3" maxlength="2000"
              placeholder="Berikan penjelasan mengapa jawaban tersebut benar..."
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm
                     placeholder-slate-300 resize-none transition
                     focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200
                     @error('explanation') border-red-400 bg-red-50 @enderror"
    >{{ old('explanation', $quiz?->explanation ?? '') }}</textarea>
    @error('explanation')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Urutan --}}
<div class="space-y-1">
    <label for="quiz_order" class="block text-xs font-semibold text-slate-700">Urutan Soal</label>
    <input type="number" id="quiz_order" name="quiz_order" min="0"
           value="{{ old('quiz_order', $quiz?->quiz_order ?? '') }}"
           placeholder="Otomatis jika kosong"
           class="w-28 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm
                  transition focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200">
</div>

{{-- ═══════════════════════════════ MEDIA ════════════════════════════════ --}}
<div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-700">Media Soal</p>
            <p class="text-xs text-slate-400">Tambahkan gambar, video, audio, atau URL pendukung soal. Maks. 5 item.</p>
        </div>
        <button type="button" id="btn-add-media"
                class="flex items-center gap-1 rounded-full bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white active:bg-indigo-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah
        </button>
    </div>

    {{-- Media yang sudah ada (hanya di edit) --}}
    @if(isset($quiz) && $quiz && $quiz->media->count())
    <div class="space-y-2" id="existing-media">
        <p class="text-xs font-medium text-slate-500">Media Saat Ini:</p>
        @foreach($quiz->media as $m)
        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm">
            {{-- Icon type --}}
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                {{ $m->media_type === 'image' ? 'bg-blue-100 text-blue-600' :
                   ($m->media_type === 'video' ? 'bg-purple-100 text-purple-600' :
                   ($m->media_type === 'audio' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600')) }}">
                @if($m->media_type === 'image')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                @elseif($m->media_type === 'video')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.723v6.554a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                @elseif($m->media_type === 'audio')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                @endif
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-slate-700 truncate">{{ $m->title ?: $m->url ?: basename($m->file_path ?? 'Media') }}</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-wide">{{ $m->media_type }}</p>
            </div>
            <label class="flex items-center gap-1.5 cursor-pointer">
                <input type="checkbox" name="delete_media[]" value="{{ $m->id }}"
                       class="h-3.5 w-3.5 rounded accent-red-500">
                <span class="text-xs text-red-500 font-medium">Hapus</span>
            </label>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Container media baru (JS-rendered) --}}
    <div id="media-container" class="space-y-3"></div>

    <p class="text-xs text-slate-400" id="media-hint">Belum ada media baru yang ditambahkan.</p>
</div>

{{-- Status Aktif --}}
<div class="space-y-1">
    <label class="block text-xs font-semibold text-slate-700">Status Quiz</label>
    <div class="flex gap-4">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="is_active" value="1"
                   {{ (int) $isActiveVal === 1 ? 'checked' : '' }}
                   class="text-indigo-600 focus:ring-indigo-300">
            <span class="text-sm text-slate-700">Aktif</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="is_active" value="0"
                   {{ (int) $isActiveVal === 0 ? 'checked' : '' }}
                   class="text-indigo-600 focus:ring-indigo-300">
            <span class="text-sm text-slate-700">Nonaktif</span>
        </label>
    </div>
    <p class="text-xs text-slate-400">Quiz aktif akan tampil untuk user.</p>
</div>

{{-- ═══════════════════ JAVASCRIPT MEDIA BUILDER ═════════════════════════ --}}
<script>
(function () {
    const container = document.getElementById('media-container');
    const hint      = document.getElementById('media-hint');
    const btnAdd    = document.getElementById('btn-add-media');
    let count = 0;

    function updateHint() {
        hint.style.display = container.children.length === 0 ? '' : 'none';
    }

    function buildRow(idx) {
        const wrap = document.createElement('div');
        wrap.className = 'rounded-xl border border-indigo-100 bg-white p-3 space-y-2 shadow-sm';
        wrap.id = 'media-row-' + idx;

        wrap.innerHTML = `
        <div class="flex items-center justify-between mb-1">
            <p class="text-xs font-semibold text-indigo-700">Media #${idx + 1}</p>
            <button type="button" onclick="removeMedia(${idx})"
                    class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-600">Tipe <span class="text-red-500">*</span></label>
                <select name="media[${idx}][media_type]" onchange="toggleMediaFields(${idx}, this.value)"
                        class="w-full rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs text-slate-700 focus:border-indigo-400 focus:outline-none">
                    <option value="">-- Pilih --</option>
                    <option value="image">Gambar</option>
                    <option value="video">Video</option>
                    <option value="audio">Audio</option>
                    <option value="url">URL</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-medium text-slate-600">Judul</label>
                <input type="text" name="media[${idx}][title]"
                       placeholder="Judul media (opsional)"
                       class="w-full rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs text-slate-700 focus:border-indigo-400 focus:outline-none">
            </div>
        </div>
        <div id="media-url-${idx}" class="space-y-1 hidden">
            <label class="text-xs font-medium text-slate-600">URL</label>
            <input type="text" name="media[${idx}][url]"
                   placeholder="https://youtube.com/..."
                   class="w-full rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs text-slate-700 focus:border-indigo-400 focus:outline-none">
        </div>
        <div id="media-file-${idx}" class="space-y-1 hidden">
            <label class="text-xs font-medium text-slate-600">Upload File</label>
            <input type="file" name="media[${idx}][file]"
                   class="w-full text-xs text-slate-600 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1 file:text-xs file:font-medium file:text-indigo-700">
        </div>
        <input type="hidden" name="media[${idx}][media_order]" value="${idx + 1}">
        `;
        return wrap;
    }

    window.toggleMediaFields = function (idx, type) {
        const urlEl  = document.getElementById('media-url-' + idx);
        const fileEl = document.getElementById('media-file-' + idx);
        urlEl.classList.add('hidden');
        fileEl.classList.add('hidden');
        if (type === 'url') urlEl.classList.remove('hidden');
        else if (['image','video','audio'].includes(type)) fileEl.classList.remove('hidden');
    };

    window.removeMedia = function (idx) {
        const row = document.getElementById('media-row-' + idx);
        if (row) row.remove();
        updateHint();
    };

    btnAdd.addEventListener('click', function () {
        if (container.children.length >= 5) {
            alert('Maksimal 5 media per soal.');
            return;
        }
        container.appendChild(buildRow(count++));
        updateHint();
    });

    updateHint();
}());
</script>
