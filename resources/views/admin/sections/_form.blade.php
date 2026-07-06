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

{{-- Judul --}}
<div class="space-y-1">
    <label for="title" class="block text-xs font-semibold text-slate-700">
        Judul Section <span class="text-red-500">*</span>
    </label>
    <input type="text" id="title" name="title"
           value="{{ old('title', $section?->title ?? '') }}"
           placeholder="Contoh: Pengenalan Laporan Keuangan"
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
    <textarea id="description" name="description" rows="3" maxlength="2000"
              placeholder="Deskripsi singkat section ini..."
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm
                     placeholder-slate-300 resize-none transition
                     focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200
                     @error('description') border-red-400 bg-red-50 @enderror"
    >{{ old('description', $section?->description ?? '') }}</textarea>
    @error('description')
    <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
    <p class="text-xs text-slate-400">Maksimal 2.000 karakter.</p>
</div>

{{-- Relasi ke Materi (Learning Schema) --}}
<div class="space-y-1">
    <label class="block text-xs font-semibold text-slate-700">
        Hubungkan ke Materi
        <span class="font-normal text-slate-400">(opsional, bisa lebih dari satu)</span>
    </label>
    <div class="rounded-xl border border-slate-200 bg-slate-50 divide-y divide-slate-100">
        @forelse($learningSchemas as $ls)
        <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-indigo-50 transition first:rounded-t-xl last:rounded-b-xl">
            <input type="checkbox"
                   name="learning_schema_ids[]"
                   value="{{ $ls->id }}"
                   {{ in_array($ls->id, old('learning_schema_ids', $attachedSchemaIds ?? [])) ? 'checked' : '' }}
                   class="h-4 w-4 rounded accent-indigo-600">
            <span class="flex-1 text-sm text-slate-700">{{ $ls->title }}</span>
            @unless($ls->is_active)
            <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-500">Nonaktif</span>
            @endunless
        </label>
        @empty
        <p class="px-4 py-3 text-xs text-slate-400 italic">Belum ada materi. Buat materi terlebih dahulu.</p>
        @endforelse
    </div>
    @error('learning_schema_ids')
    <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Status Aktif --}}
<div class="space-y-1">
    <label class="block text-xs font-semibold text-slate-700">Status Section</label>
    <div class="flex gap-4">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="is_active" value="1" class="text-indigo-600 focus:ring-indigo-300"
                {{ old('is_active', $section ? ($section->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }}>
            <span class="text-sm text-slate-700">Aktif</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="is_active" value="0" class="text-indigo-600 focus:ring-indigo-300"
                {{ old('is_active', $section ? ($section->is_active ? '1' : '0') : '1') == '0' ? 'checked' : '' }}>
            <span class="text-sm text-slate-700">Nonaktif</span>
        </label>
    </div>
    <p class="text-xs text-slate-400">Section aktif akan tampil dan bisa diakses user.</p>
</div>
