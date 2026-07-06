<x-admin-layout title="Tambah Learning Schema">
<div class="px-4 pt-5 pb-10">

    {{-- Breadcrumb --}}
    <div class="mb-5 flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('admin.learning-schemas.index') }}" class="hover:text-indigo-600 transition">Learning Schema</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-slate-600 font-medium">Tambah Baru</span>
    </div>

    <div class="max-w-xl">
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Tambah Learning Schema</h2>
                <p class="text-xs text-slate-400 mt-0.5">Isi detail learning schema yang akan ditambahkan.</p>
            </div>
            <form method="POST" action="{{ route('admin.learning-schemas.store') }}" class="px-5 py-5 space-y-4">
                @csrf
                @include('admin.learning-schemas._form', ['learningSchema' => null])
                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow hover:bg-indigo-700 active:bg-indigo-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan
                    </button>
                    <a href="{{ route('admin.learning-schemas.index') }}"
                       class="inline-flex items-center rounded-full border border-slate-200 bg-white px-5 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</x-admin-layout>
