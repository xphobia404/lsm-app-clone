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

{{-- Upload Gambar Soal (opsional) --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">
        Gambar Soal <span class="ml-1 font-normal text-slate-400">(opsional)</span>
    </label>

    {{-- Preview gambar yang sudah ada (saat edit) --}}
    @if(!empty($quiz->question_image ?? null))
    <div class="mb-3" id="existing-image-wrapper">
        <img src="{{ Storage::url($quiz->question_image) }}"
             alt="Gambar soal" id="existing-preview"
             class="mb-2 max-h-48 rounded-xl border border-slate-200 object-contain">
        <label class="flex cursor-pointer items-center gap-2 text-xs text-red-500">
            <input type="checkbox" name="remove_image" value="1" id="remove_image"
                   onchange="toggleRemoveImage(this)">
            Hapus gambar ini
        </label>
    </div>
    @endif

    {{-- Input file --}}
    <div id="file-input-wrapper" class="{{ !empty($quiz->question_image ?? null) ? 'hidden' : '' }}">
        <label for="question_image"
               class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-6 hover:border-indigo-400 hover:bg-indigo-50 transition">
            <svg class="mb-2 h-8 w-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 12h18M3 6h18"/>
            </svg>
            <span class="text-xs text-slate-500">Klik untuk pilih gambar</span>
            <span class="text-xs text-slate-400">JPG, PNG, WebP &mdash; maks. 2MB</span>
            <input type="file" name="question_image" id="question_image"
                   accept="image/jpeg,image/png,image/webp"
                   class="hidden"
                   onchange="previewImage(this)">
        </label>

        {{-- Preview setelah pilih file baru --}}
        <div id="new-preview-wrapper" class="mt-2 hidden">
            <img id="new-preview-img" src="" alt="Preview"
                 class="max-h-48 rounded-xl border border-slate-200 object-contain">
            <button type="button" onclick="clearImageInput()"
                    class="mt-1 text-xs text-red-400 hover:text-red-600">
                Batalkan pilihan
            </button>
        </div>
    </div>
</div>

{{-- Info opsi --}}
<div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-2.5 text-xs text-blue-600">
    <strong>Info:</strong> Opsi A wajib diisi. Opsi B, C, D bersifat opsional &mdash; isi sesuai kebutuhan (minimal 1, maksimal 4 opsi).
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

{{-- Opsi B, C, D (opsional) --}}
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
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('new-preview-img').src = e.target.result;
            document.getElementById('new-preview-wrapper').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImageInput() {
    document.getElementById('question_image').value = '';
    document.getElementById('new-preview-wrapper').classList.add('hidden');
}

function toggleRemoveImage(checkbox) {
    const fileWrapper = document.getElementById('file-input-wrapper');
    if (checkbox.checked) {
        fileWrapper.classList.remove('hidden');
    } else {
        fileWrapper.classList.add('hidden');
    }
}

function syncAnswerOptions() {
    const keys = ['a', 'b', 'c', 'd'];
    let firstVisible = null;

    keys.forEach(function(key) {
        const input  = document.getElementById('option_' + key);
        const label  = document.getElementById('answer-label-' + key);
        const radio  = label ? label.querySelector('input[type="radio"]') : null;

        if (!input || !label) return;

        const hasValue = input.value.trim() !== '';

        if (key === 'a') {
            label.classList.remove('hidden');
            firstVisible = key;
            return;
        }

        if (hasValue) {
            label.classList.remove('hidden');
            if (firstVisible === null) firstVisible = key;
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
