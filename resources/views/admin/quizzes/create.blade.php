<x-admin-layout :title="'Tambah Soal — ' . $section->title">
    <div class="px-4 pt-5 pb-10">
        <div class="mb-4 flex items-center gap-2">
            <a href="{{ route('admin.sections.quizzes.index', $section) }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
            <h2 class="text-base font-bold text-slate-800">Tambah Soal Quiz</h2>
        </div>
        <form method="POST" action="{{ route('admin.sections.quizzes.store', $section) }}" class="space-y-4">
            @csrf
            @include('admin.quizzes._form')
            <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-full bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm active:scale-[0.98] transition">
                Simpan Soal
            </button>
        </form>
    </div>
</x-admin-layout>
