{{-- Validation errors --}}
@if($errors->any())
    <div class="rounded-xl bg-red-50 border border-red-100 px-4 py-3">
        <p class="mb-1 text-xs font-semibold text-red-600">Terdapat kesalahan input:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)
                <li class="text-xs text-red-500">{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Judul --}}
<div>
    <label for="title" class="mb-1.5 block text-xs font-semibold text-slate-700">
        Judul Section <span class="text-red-500">*</span>
    </label>
    <input type="text" id="title" name="title"
           value="{{ old('title', $section->title ?? '') }}"
           class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800
                  placeholder-slate-300 transition
                  focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100
                  @error('title') border-red-400 bg-red-50 @enderror"
           placeholder="Contoh: Pengenalan Laporan Keuangan" required maxlength="255">
    @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Deskripsi --}}
<div>
    <label for="description" class="mb-1.5 block text-xs font-semibold text-slate-700">
        Deskripsi <span class="text-slate-400 font-normal">(opsional)</span>
    </label>
    <textarea id="description" name="description" rows="3" maxlength="2000"
              class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800
                     placeholder-slate-300 transition resize-none
                     focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100
                     @error('description') border-red-400 bg-red-50 @enderror"
              placeholder="Deskripsi singkat section ini...">{{ old('description', $section->description ?? '') }}</textarea>
    @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Relasi ke Materi (Learning Schema) --}}
<div>
    <label class="mb-1.5 block text-xs font-semibold text-slate-700">
        Hubungkan ke Materi
        <span class="text-slate-400 font-normal">(opsional, bisa lebih dari satu)</span>
    </label>
    <div class="space-y-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        @forelse($learningSchemas as $ls)
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox"
                       name="learning_schema_ids[]"
                       value="{{ $ls->id }}"
                       {{ in_array($ls->id, old('learning_schema_ids', $attachedSchemaIds ?? [])) ? 'checked' : '' }}
                       class="h-4 w-4 rounded accent-indigo-600">
                <span class="text-xs font-medium text-slate-700">{{ $ls->title }}</span>
                @unless($ls->is_active)
                    <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[10px] text-slate-500">Non-aktif</span>
                @endunless
            </label>
        @empty
            <p class="text-xs text-slate-400 italic">Belum ada materi. Buat materi terlebih dahulu.</p>
        @endforelse
    </div>
    @error('learning_schema_ids')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

{{-- Status Aktif --}}
<div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
    <div class="pt-0.5">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1"
               {{ old('is_active', $section->is_active ?? true) ? 'checked' : '' }}
               class="h-4 w-4 rounded accent-indigo-600 cursor-pointer">
    </div>
    <div>
        <label for="is_active" class="block text-xs font-semibold text-slate-700 cursor-pointer">Aktifkan Section</label>
        <p class="text-xs text-slate-400 mt-0.5">Section yang aktif akan tampil untuk pengguna.</p>
    </div>
</div>
