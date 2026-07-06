{{-- Shared form partial for create & edit quiz --}}

@if($errors->any())
<x-alert type="error" class="mb-2">
    <ul class="list-disc list-inside text-xs space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</x-alert>
@endif

{{-- Urutan Soal --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Urutan Soal</label>
    <input type="number" name="order" value="{{ old('order', $quiz->order ?? 0) }}" min="0"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
</div>

{{-- Pertanyaan --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Pertanyaan <span class="text-red-500">*</span></label>
    <textarea name="question" rows="3" required
              class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Tulis pertanyaan...">{{ old('question', $quiz->question ?? '') }}</textarea>
</div>

{{-- Media yang sudah ada (hanya saat edit) --}}
@if(!empty($quiz->id) && $quiz->media->isNotEmpty())
<div>
    <label class="mb-2 block text-xs font-semibold text-slate-700">Media Terlampir</label>
    <div class="grid grid-cols-2 gap-2">
        @foreach($quiz->media as $m)
        <div class="relative rounded-xl border border-slate-200 bg-slate-50 overflow-hidden">
            @if($m->type === 'image')
                <img src="{{ $m->url }}" alt="{{ $m->original_name }}"
                     class="h-28 w-full object-contain p-1">
            @elseif($m->type === 'video')
                <video src="{{ $m->url }}" controls
                       class="h-28 w-full object-contain bg-black"></video>
            @elseif($m->type === 'audio')
                <div class="flex flex-col items-center justify-center h-28 px-2">
                    <svg class="h-8 w-8 text-indigo-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                    <audio src="{{ $m->url }}" controls class="w-full"></audio>
                </div>
            @endif
            <p class="truncate px-2 py-1 text-xs text-slate-500">{{ $m->original_name }}</p>
            {{-- Tombol hapus --}}
            <form method="POST" action="{{ route('admin.media.destroy', $m) }}"
                  onsubmit="return confirm('Hapus media ini?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="absolute top-1 right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white text-xs shadow hover:bg-red-600">
                    &times;
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Upload Media Baru --}}
@if(!empty($quiz->id))
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">
        Upload Media <span class="ml-1 font-normal text-slate-400">(foto, video, audio — bisa banyak)</span>
    </label>
    <form method="POST"
          action="{{ route('admin.sections.quizzes.media.store', [$section, $quiz]) }}"
          enctype="multipart/form-data"
          class="space-y-2">
        @csrf
        <label for="media-files"
               class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
            <svg class="mb-1 h-8 w-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
            </svg>
            <span class="text-xs text-slate-500">Klik atau drag file ke sini</span>
            <span class="text-xs text-slate-400">JPG, PNG, WebP, GIF, MP4, MOV, MP3, WAV — maks. 50MB/file</span>
            <input type="file" id="media-files" name="files[]" multiple
                   accept="image/*,video/mp4,video/quicktime,video/x-msvideo,audio/mpeg,audio/wav,audio/ogg"
                   class="hidden" onchange="previewMediaFiles(this)">
        </label>
        {{-- Preview sebelum upload --}}
        <div id="media-preview" class="grid grid-cols-3 gap-2 hidden"></div>
        <button type="submit"
                class="w-full rounded-full bg-indigo-600 py-2.5 text-xs font-semibold text-white transition active:scale-[0.98]">
            Upload Media
        </button>
    </form>
</div>
@else
<div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-2.5 text-xs text-amber-700">
    <strong>Info:</strong> Simpan soal terlebih dahulu, lalu upload media melalui halaman Edit.
</div>
@endif

{{-- Info opsi --}}
<div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-2.5 text-xs text-blue-600">
    <strong>Info:</strong> Opsi A wajib diisi. Opsi B, C, D bersifat opsional.
</div>

{{-- Opsi A (wajib) --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">
        Opsi A <span class="text-red-500">*</span>
        <span class="ml-1 font-normal text-slate-400">(wajib)</span>
    </label>
    <input type="text" name="option_a" id="option_a"
           value="{{ old('option_a', $quiz->option_a ?? '') }}"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="Isi opsi A" required
           oninput="syncAnswerOptions()">
</div>

@foreach(['b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">
        Opsi {{ $label }}
        <span class="ml-1 font-normal text-slate-400">(opsional)</span>
    </label>
    <input type="text" name="option_{{ $key }}" id="option_{{ $key }}"
           value="{{ old('option_'.$key, $quiz->{'option_'.$key} ?? '') }}"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="Kosongkan jika tidak dipakai"
           oninput="syncAnswerOptions()">
</div>
@endforeach

{{-- Jawaban Benar --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Jawaban Benar <span class="text-red-500">*</span></label>
    <p class="mb-2 text-xs text-slate-400">Hanya opsi yang sudah diisi yang bisa dipilih sebagai jawaban benar.</p>
    <div class="grid grid-cols-4 gap-2" id="answer-options-container">
        @foreach(['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)
        <label id="answer-label-{{ $key }}"
               class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border py-3 text-sm font-bold uppercase
                      has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:text-indigo-700
                      border-slate-200 text-slate-500 transition
                      {{ ($key !== 'a' && empty(old('option_'.$key, $quiz->{'option_'.$key} ?? ''))) ? 'hidden' : '' }}">
            <input type="radio" name="correct_answer" value="{{ $key }}"
                   {{ old('correct_answer', $quiz->correct_answer ?? '') === $key ? 'checked' : '' }}
                   class="sr-only" required>
            {{ $label }}
        </label>
        @endforeach
    </div>
</div>

{{-- Penjelasan --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Penjelasan Jawaban <span class="text-slate-400">(opsional)</span></label>
    <textarea name="explanation" rows="2"
              class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Jelaskan kenapa jawaban tersebut benar...">{{ old('explanation', $quiz->explanation ?? '') }}</textarea>
</div>

<script>
function previewMediaFiles(input) {
    const preview = document.getElementById('media-preview');
    preview.innerHTML = '';
    if (!input.files || !input.files.length) { preview.classList.add('hidden'); return; }
    preview.classList.remove('hidden');
    Array.from(input.files).forEach(file => {
        const wrap = document.createElement('div');
        wrap.className = 'rounded-xl border border-slate-200 bg-slate-50 overflow-hidden';
        const url = URL.createObjectURL(file);
        if (file.type.startsWith('image/')) {
            wrap.innerHTML = `<img src="${url}" class="h-20 w-full object-contain p-1"><p class="truncate px-1 py-0.5 text-xs text-slate-500">${file.name}</p>`;
        } else if (file.type.startsWith('video/')) {
            wrap.innerHTML = `<video src="${url}" class="h-20 w-full object-contain bg-black"></video><p class="truncate px-1 py-0.5 text-xs text-slate-500">${file.name}</p>`;
        } else {
            wrap.innerHTML = `<div class="flex h-20 items-center justify-center text-indigo-400"><svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg></div><p class="truncate px-1 py-0.5 text-xs text-slate-500">${file.name}</p>`;
        }
        preview.appendChild(wrap);
    });
}

function syncAnswerOptions() {
    const keys = ['a', 'b', 'c', 'd'];
    keys.forEach(function(key) {
        const input = document.getElementById('option_' + key);
        const label = document.getElementById('answer-label-' + key);
        const radio = label ? label.querySelector('input[type="radio"]') : null;
        if (!input || !label) return;
        if (key === 'a') { label.classList.remove('hidden'); return; }
        if (input.value.trim() !== '') {
            label.classList.remove('hidden');
        } else {
            label.classList.add('hidden');
            if (radio && radio.checked) {
                const radioA = document.querySelector('input[name="correct_answer"][value="a"]');
                if (radioA) radioA.checked = true;
            }
        }
    });
}
document.addEventListener('DOMContentLoaded', syncAnswerOptions);
</script>
