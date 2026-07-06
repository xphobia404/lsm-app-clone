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
    // 1. old input (setelah validation fail) → pakai old
    // 2. edit (ada $learningSchema) → cast ke int dari DB
    // 3. create (null) → default aktif = 1
    $isActiveVal = old('is_active', $learningSchema ? (int) $learningSchema->is_active : 1);
@endphp

{{-- Judul --}}
<div class="space-y-1">
    <label for="title" class="block text-xs font-semibold text-slate-700">
        Judul Materi <span class="text-red-500">*</span>
    </label>
    <input type="text" id="title" name="title"
           value="{{ old('title', $learningSchema?->title ?? '') }}"
           placeholder="Contoh: Dasar-dasar Akuntansi"
           maxlength="255" required
           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm
                  placeholder-slate-300 transition
                  focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200
                  @error('title') border-red-400 bg-red-50 @enderror">
    @error('title')
    <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Deskripsi --}}
<div class="space-y-1">
    <label for="description" class="block text-xs font-semibold text-slate-700">
        Deskripsi
        <span class="font-normal text-slate-400">(opsional)</span>
    </label>
    <textarea id="description" name="description" rows="4" maxlength="1000"
              placeholder="Deskripsi singkat mengenai materi ini..."
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm
                     placeholder-slate-300 resize-none transition
                     focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200
                     @error('description') border-red-400 bg-red-50 @enderror"
    >{{ old('description', $learningSchema?->description ?? '') }}</textarea>
    @error('description')
    <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
    <p class="text-xs text-slate-400">Maksimal 1.000 karakter.</p>
</div>

{{-- Status Aktif --}}
<div class="space-y-1">
    <label class="block text-xs font-semibold text-slate-700">Status Materi</label>
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
    <p class="text-xs text-slate-400">Materi aktif akan tampil dan bisa diakses user.</p>
</div>
