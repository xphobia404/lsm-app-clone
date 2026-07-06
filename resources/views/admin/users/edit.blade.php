<x-admin-layout :title="'Edit: ' . ($user->name ?: $user->username)">
<div class="px-4 pt-5 pb-10">
    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <div class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            <h2 class="text-base font-bold text-slate-800">Edit User</h2>
        </div>
    </div>
    @include('admin.users._form', [
        'method' => 'PUT',
        'action' => route('admin.users.update', $user),
        'user'   => $user,
    ])
</div>
</x-admin-layout>
