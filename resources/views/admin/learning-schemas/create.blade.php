<x-admin-layout title="Tambah Materi">
<div class="px-4 pt-5 pb-10">

    {{-- Header --}}
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.learning-schemas.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <h2 class="text-base font-bold text-slate-800">Tambah Materi</h2>
        </div>
    </div>

    @if(session('success'))
    <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
    @endif

    {{-- Card --}}
    <div class="mb-3 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-4 text-white shadow-md">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm">Materi Baru</p>
                <p class="text-xs text-indigo-200">Isi form di bawah untuk menambahkan materi pembelajaran.</p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.learning-schemas.store') }}" class="space-y-4">
        @csrf
        @include('admin.learning-schemas._form', ['learningSchema' => null])

        <div class="pt-2 flex gap-3">
            <button type="submit"
                class="flex-1 rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-indigo-700 transition">
                Simpan Materi
            </button>
            <a href="{{ route('admin.learning-schemas.index') }}"
               class="flex-1 rounded-full border border-slate-200 bg-slate-50 py-2.5 text-center text-sm font-medium text-slate-600 active:bg-slate-100 transition">
                Batal
            </a>
        </div>
    </form>

</div>
</x-admin-layout>
