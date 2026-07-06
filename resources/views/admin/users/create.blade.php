<x-admin-layout title="Tambah User">
<div class="px-4 pt-5 pb-10">
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <h2 class="text-base font-bold text-slate-800">Tambah User</h2>
        </div>
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
