{{-- Shared form partial for course-type create & edit --}}

@if($errors->any())
<x-alert type="error" class="mb-2">
    <ul class="list-disc list-inside text-xs space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</x-alert>
@endif

{{-- Icon (Tabler Icons) --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">
        Icon <span class="text-slate-400">(nama Tabler Icon, opsional)</span>
    </label>
    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
            <i class="ti ti-{{ old('icon', $courseType->icon ?? 'book') }} text-xl" id="icon-preview"></i>
        </span>
        <input type="text" name="icon" id="icon-input"
               value="{{ old('icon', $courseType->icon ?? '') }}"
               class="block w-full rounded-2xl border border-slate-300 py-3 pl-12 pr-4 text-sm focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="book-open">
    </div>
    <p class="mt-1 text-xs text-slate-400">
        Contoh: <code class="bg-slate-100 px-1 rounded">book-open</code>,
        <code class="bg-slate-100 px-1 rounded">users</code>,
        <code class="bg-slate-100 px-1 rounded">briefcase</code>,
        <code class="bg-slate-100 px-1 rounded">award</code>,
        <code class="bg-slate-100 px-1 rounded">heart</code>.
        Cek lengkap di <a href="https://tabler.io/icons" target="_blank" class="text-indigo-500 underline">tabler.io/icons</a>.
    </p>
</div>

{{-- Live preview icon --}}
<script>
    document.getElementById('icon-input')?.addEventListener('input', function () {
        const preview = document.getElementById('icon-preview');
        if (preview) {
            preview.className = 'ti ti-' + (this.value.trim() || 'book') + ' text-xl';
        }
    });
</script>

{{-- Nama --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Nama Spesialisasi <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $courseType->name ?? '') }}"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="Contoh: P3K, HSE, K3 Umum" required>
</div>

{{-- Deskripsi --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Deskripsi</label>
    <textarea name="description" rows="3"
              class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Deskripsi singkat spesialisasi ini">{{ old('description', $courseType->description ?? '') }}</textarea>
</div>

{{-- Urutan --}}
<div>
    <label class="mb-1 block text-xs font-semibold text-slate-700">Urutan Tampil</label>
    <input type="number" name="order" value="{{ old('order', $courseType->order ?? 1) }}" min="0"
           class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
</div>

{{-- Status Aktif --}}
<label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1"
           {{ old('is_active', $courseType->is_active ?? true) ? 'checked' : '' }}
           class="h-5 w-5 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
    <div>
        <p class="text-sm font-medium text-slate-800">Aktifkan Spesialisasi</p>
        <p class="text-xs text-slate-500">Spesialisasi akan tampil di pilihan user jika diaktifkan.</p>
    </div>
</label>
