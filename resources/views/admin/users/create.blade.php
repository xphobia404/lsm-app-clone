<x-admin-layout title="Tambah User">
<div class="px-4 pt-5 pb-10">
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <h2 class="text-base font-bold text-slate-800">Tambah User</h2>
        </div>
    </div>

    <div class="mb-4 flex items-center justify-between rounded-2xl border border-indigo-100 bg-indigo-50/60 px-4 py-3">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <div>
                <p class="text-xs font-semibold text-slate-800">Ingin buat banyak user sekaligus?</p>
                <p class="text-[11px] text-slate-500">Gunakan fitur Import CSV untuk mengunggah daftar user.</p>
            </div>
        </div>
        <a href="{{ route('admin.users.index') }}" class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">
            Import CSV &rarr;
        </a>
    </div>
    @include('admin.users._form', [
        'method'      => 'POST',
        'action'      => route('admin.users.store'),
        'user'        => null,
        'allSchemas'  => $allSchemas,
        'enrolledIds' => [],
        'enrollments' => collect(),
    ])
</div>
</x-admin-layout>
