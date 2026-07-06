<x-admin-layout :title="'Edit: ' . $learningSchema->title">
<div class="px-4 pt-5 pb-10">
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.learning-schemas.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <h2 class="text-base font-bold text-slate-800">Edit Learning Schema</h2>
    </div>
    <form method="POST" action="{{ route('admin.learning-schemas.update', $learningSchema) }}" class="space-y-4">
        @csrf @method('PUT')
        @include('admin.learning-schemas._form', ['learningSchema' => $learningSchema])
        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-700 active:bg-indigo-800 transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.learning-schemas.index') }}"
               class="inline-flex items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Batal</a>
        </div>
    </form>
</div>
</x-admin-layout>
