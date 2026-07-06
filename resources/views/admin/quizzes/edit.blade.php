<x-admin-layout :title="'Edit Quiz #' . $quiz->quiz_order">
<div class="px-4 pt-5 pb-10">

    {{-- Header --}}
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.sections.quizzes.index', $section) }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <h2 class="text-base font-bold text-slate-800">Edit Quiz</h2>
        </div>
    </div>

    {{-- Info card --}}
    <div class="mb-3 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 p-4 text-white shadow-md">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-sm font-extrabold">
                #{{ $quiz->quiz_order }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm line-clamp-2">{{ Str::limit($quiz->question, 80) }}</p>
                <p class="text-xs text-amber-100 mt-0.5">
                    Section: {{ $section->title }}
                    &bull; {{ $quiz->media->count() }} media
                </p>
            </div>
            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold
                {{ $quiz->is_active ? 'bg-emerald-400/20 text-emerald-100' : 'bg-red-400/20 text-red-100' }}">
                {{ $quiz->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.sections.quizzes.update', [$section, $quiz]) }}"
          enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        @include('admin.quizzes._form', ['quiz' => $quiz])

        <div class="pt-2 flex gap-3">
            <button type="submit"
                class="flex-1 rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-indigo-700 transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.sections.quizzes.index', $section) }}"
               class="flex-1 rounded-full border border-slate-200 bg-slate-50 py-2.5 text-center text-sm font-medium text-slate-600 active:bg-slate-100 transition">
                Batal
            </a>
        </div>
    </form>

</div>
</x-admin-layout>
