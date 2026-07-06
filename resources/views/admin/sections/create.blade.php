<x-admin-layout title="Tambah Section">
<div class="px-4 pt-5 pb-10">

    {{-- Header --}}
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.sections.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <h2 class="text-base font-bold text-slate-800">Tambah Section</h2>
        </div>
    </div>

    @if(session('success'))
    <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
    @endif

    {{-- Info card --}}
    <div class="mb-3 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 p-4 text-white shadow-md">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm">Section Baru</p>
                <p class="text-xs text-violet-200">Isi form di bawah untuk menambahkan section pembelajaran.</p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.sections.store') }}" class="space-y-4">
        @csrf
        @include('admin.sections._form', ['section' => null])

        <div class="pt-2 flex gap-3">
            <button type="submit"
                class="flex-1 rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-indigo-700 transition">
                Simpan Section
            </button>
            <a href="{{ route('admin.sections.index') }}"
               class="flex-1 rounded-full border border-slate-200 bg-slate-50 py-2.5 text-center text-sm font-medium text-slate-600 active:bg-slate-100 transition">
                Batal
            </a>
        </div>
    </form>

</div>
</x-admin-layout>
