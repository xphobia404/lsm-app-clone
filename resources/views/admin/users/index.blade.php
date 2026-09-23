<x-admin-layout title="Kelola Users">
<div class="px-4 pt-5 pb-10 space-y-4">

    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <h2 class="text-base font-bold text-slate-800">Kelola Users</h2>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    onclick="document.getElementById('import-csv-container').classList.toggle('hidden')"
                    class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-xs font-semibold text-indigo-600 shadow-sm active:bg-indigo-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import CSV
            </button>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm active:bg-indigo-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </a>
        </div>
    </div>

    {{-- CSV Import Box --}}
    <div id="import-csv-container" class="{{ $errors->has('csv_file') || session('import_errors') ? '' : 'hidden' }} rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <div>
                <h3 class="text-xs font-bold text-slate-800">Import Multi User via CSV</h3>
                <p class="text-[11px] text-slate-500">Unggah file CSV untuk membuat banyak akun sekaligus.</p>
            </div>
            <a href="{{ route('admin.users.template') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download Template CSV
            </a>
        </div>

        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih File CSV <span class="text-red-500">*</span></label>
                <input type="file" name="csv_file" accept=".csv,.txt"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700 focus:border-indigo-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-200"
                       required>
                <p class="mt-1 text-[11px] text-slate-400">Format kolom CSV: <code class="rounded bg-slate-100 px-1 text-slate-600">name,username,email,password,role,is_active</code></p>
                @error('csv_file')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            @if(isset($allSchemas) && $allSchemas->count() > 0)
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-700">Enrollment Materi Otomatis (Opsional)</label>
                <p class="text-[11px] text-slate-400">Seluruh user yang diimpor akan langsung didaftarkan ke materi berikut:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-36 overflow-y-auto p-1">
                    @foreach($allSchemas as $schema)
                    <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700 cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition">
                        <input type="checkbox" name="schema_ids[]" value="{{ $schema->id }}" class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300">
                        <span class="truncate font-medium">{{ $schema->title }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex justify-end gap-2 pt-1">
                <button type="button"
                        onclick="document.getElementById('import-csv-container').classList.add('hidden')"
                        class="rounded-full border border-slate-200 bg-slate-50 px-3.5 py-1.5 text-xs font-medium text-slate-600 active:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-full bg-indigo-600 px-4 py-1.5 text-xs font-semibold text-white shadow-sm active:bg-indigo-700 transition">
                    Unggah & Import User
                </button>
            </div>
        </form>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-2 flex-wrap">
        <input
            type="text" name="search" value="{{ request('search') }}"
            placeholder="Cari nama / username / email..."
            class="flex-1 min-w-[140px] rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
        >
        <select name="role" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>User</option>
        </select>
        <select name="status" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="rounded-full bg-slate-100 px-4 py-2 text-xs font-medium text-slate-600 active:bg-slate-200 transition">Cari</button>
        @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users.index') }}" class="rounded-full bg-red-50 px-4 py-2 text-xs font-medium text-red-500 active:bg-red-100 transition">Reset</a>
        @endif
    </form>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    @if(session('import_errors'))
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-3.5 text-xs text-amber-800 space-y-1.5">
        <p class="font-bold flex items-center gap-1.5 text-amber-900">
            <svg class="h-4 w-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Peringatan Import CSV:
        </p>
        <ul class="list-disc list-inside space-y-0.5 text-slate-700 max-h-40 overflow-y-auto">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Info jumlah --}}
    @if($users->total() > 0)
    <p class="text-xs text-slate-400">
        Menampilkan {{ $users->firstItem() }}&ndash;{{ $users->lastItem() }} dari {{ $users->total() }} user
    </p>
    @endif

    @if($users->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <p class="text-sm font-medium text-slate-500">Belum ada user</p>
        <p class="text-xs text-slate-400 mt-1">Tambahkan user pertama untuk memulai.</p>
    </div>
    @else
    <div class="space-y-2.5">
        @foreach($users as $user)
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Row 1: avatar + info --}}
            <div class="flex items-center gap-3 px-4 pt-3.5 pb-3">
                {{-- Avatar inisial --}}
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 text-sm font-bold">
                    {{ strtoupper(substr($user->name ?: $user->username, 0, 1)) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <p class="text-sm font-semibold text-slate-800 break-words">{{ $user->name ?: $user->username }}</p>
                        <span class="shrink-0 rounded-full px-1.5 py-0.5 text-[10px] font-medium
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-600' : 'bg-slate-100 text-slate-500' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        @if($user->is_active)
                            <span class="shrink-0 rounded-full bg-green-100 px-1.5 py-0.5 text-[10px] font-medium text-green-600">Aktif</span>
                        @else
                            <span class="shrink-0 rounded-full bg-red-100 px-1.5 py-0.5 text-[10px] font-medium text-red-600">Nonaktif</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400">&#64;{{ $user->username }}</p>
                    @if($user->email)
                    <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                    @endif
                </div>
            </div>

            {{-- Divider --}}
            <div class="border-t border-slate-100 mx-4"></div>

            {{-- Row 2: actions (horizontal scrollable) --}}
            <div class="flex items-center gap-2 overflow-x-auto px-4 py-2.5"
                 style="scrollbar-width:none;-webkit-overflow-scrolling:touch">

                <a href="{{ route('admin.users.show', $user) }}"
                   class="inline-flex shrink-0 items-center gap-1 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 active:bg-indigo-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Detail
                </a>

                <a href="{{ route('admin.users.edit', $user) }}"
                   class="inline-flex shrink-0 items-center gap-1 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 active:bg-slate-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit
                </a>

                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="shrink-0">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-medium transition
                        {{ $user->is_active
                            ? 'border border-amber-200 bg-amber-50 text-amber-700 active:bg-amber-100'
                            : 'border border-green-200 bg-green-50 text-green-700 active:bg-green-100' }}">
                        @if($user->is_active)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Nonaktifkan
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Aktifkan
                        @endif
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="shrink-0"
                      onsubmit="return confirm('Hapus user {{ addslashes($user->name ?: $user->username) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 active:bg-red-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </form>

            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="pt-2">
        {{ $users->links() }}
    </div>
    @endif
    @endif

</div>
</x-admin-layout>
