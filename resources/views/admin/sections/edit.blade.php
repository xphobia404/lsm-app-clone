<x-admin-layout :title="'Edit Section: ' . $section->title">
<div class="px-4 pt-5 pb-10">

    <div class="mb-5 flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ route('admin.sections.index') }}" class="hover:text-indigo-600 transition">Section</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-600 font-medium">Edit</span>
    </div>

    <div class="max-w-xl">
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Edit Section</h2>
                <p class="mt-0.5 text-xs text-slate-400 truncate">{{ $section->title }}</p>
            </div>
            <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="px-5 py-5 space-y-4">
                @csrf @method('PUT')
                @include('admin.sections._form')
                <div class="flex items-center gap-3 pt-1">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2.5 text-xs font-semibold text-white shadow hover:bg-indigo-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.sections.index') }}"
                       class="inline-flex items-center rounded-full border border-slate-200 bg-white px-5 py-2.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</x-admin-layout>
