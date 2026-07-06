@if($errors->any())
<x-alert type="error" class="mb-2">
    <ul class="list-disc list-inside text-xs space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</x-alert>
@endif

<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Judul <span class="text-red-500">*</span></label>
    <input type="text" name="title" value="{{ old('title', $learningSchema->title ?? '') }}"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="Judul learning schema" required maxlength="255">
</div>

<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Deskripsi <span class="text-slate-400">(opsional)</span></label>
    <textarea name="description" rows="3" maxlength="1000"
              class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Deskripsi singkat">{{ old('description', $learningSchema->description ?? '') }}</textarea>
</div>

<div class="flex items-center gap-3">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $learningSchema->is_active ?? true) ? 'checked' : '' }}
               class="rounded accent-indigo-600">
        <span class="text-xs font-semibold text-slate-700">Aktif</span>
    </label>
</div>
