<x-admin-layout title="Tambah Quiz">
<div class="px-4 pt-5 pb-10">

    {{-- Header --}}
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.sections.quizzes.index', $section) }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <h2 class="text-base font-bold text-slate-800">Tambah Quiz</h2>
        </div>
    </div>

    {{-- Info card --}}
    <div class="mb-3 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 p-4 text-white shadow-md">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm">Quiz Baru</p>
                <p class="text-xs text-amber-100">Section: <span class="font-semibold">{{ $section->title }}</span></p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.sections.quizzes.store', $section) }}"
          enctype="multipart/form-data" class="space-y-4">
        @csrf
        @include('admin.quizzes._form', ['quiz' => null])

        <div class="pt-2 flex gap-3">
            <button type="submit"
                class="flex-1 rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-indigo-700 transition">
                Simpan Quiz
            </button>
            <a href="{{ route('admin.sections.quizzes.index', $section) }}"
               class="flex-1 rounded-full border border-slate-200 bg-slate-50 py-2.5 text-center text-sm font-medium text-slate-600 active:bg-slate-100 transition">
                Batal
            </a>
        </div>
    </form>

</div>
</x-admin-layout>
