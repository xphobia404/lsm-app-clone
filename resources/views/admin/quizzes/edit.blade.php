<x-admin-layout title="Edit Soal">
    <div class="px-4 pt-5 pb-10">
        <div class="mb-4 flex items-center gap-2">
            <a href="{{ route('admin.sections.quizzes.index', $section) }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
            <h2 class="text-base font-bold text-slate-800">Edit Soal Quiz</h2>
        </div>
        <form method="POST" action="{{ route('admin.sections.quizzes.update', [$section, $quiz]) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.quizzes._form')
            <button type="submit"
                    class="inline-flex w-full items-center justify-center rounded-full bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm active:scale-[0.98] transition">
                Update Soal
            </button>
        </form>
    </div>
</x-admin-layout>
