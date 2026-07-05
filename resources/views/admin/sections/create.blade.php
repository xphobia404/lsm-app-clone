<x-admin-layout title="Tambah Section">
<div class="px-4 pt-5 pb-10">
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.sections.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <h2 class="text-base font-bold text-slate-800">Tambah Section</h2>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.sections.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @include('admin.sections._form', ['section' => null])
        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-700 active:bg-indigo-800 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Simpan Section
            </button>
            <a href="{{ route('admin.sections.index') }}"
               class="inline-flex items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
</x-admin-layout>
