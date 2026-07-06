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
    @error('title')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
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
              placeholder="Deskripsi singkat mengenai section ini...">{{ old('description', $section->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>

{{-- Urutan --}}
<div>
    <label for="section_order" class="mb-1.5 block text-xs font-semibold text-slate-700">
        Urutan <span class="text-red-500">*</span>
    </label>
    <input type="number" id="section_order" name="section_order" min="1"
           value="{{ old('section_order', $section->section_order ?? $nextOrder ?? 1) }}"
           class="block w-32 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800
                  transition focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100
                  @error('section_order') border-red-400 bg-red-50 @enderror"
           required>
    <p class="mt-1 text-xs text-slate-400">Urutan tampil section dalam schema.</p>
    @error('section_order')
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
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
