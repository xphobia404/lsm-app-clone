<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    @if($errors->any())
    <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
            <li class="text-xs text-red-600">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Name --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user?->name) }}"
            placeholder="Masukkan nama lengkap"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('name') border-red-400 bg-red-50 @enderror"
            required>
        @error('name')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Username --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Username <span class="text-red-500">*</span></label>
        <input type="text" name="username" value="{{ old('username', $user?->username) }}"
            placeholder="Contoh: budi123"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('username') border-red-400 bg-red-50 @enderror"
            required autocomplete="username">
        @error('username')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Email --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email', $user?->email) }}"
            placeholder="Contoh: budi@email.com"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('email') border-red-400 bg-red-50 @enderror"
            required autocomplete="email">
        @error('email')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Password --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">
            Password
            @if($user) <span class="font-normal text-slate-400">(kosongkan jika tidak diganti)</span>
            @else <span class="text-red-500">*</span> @endif
        </label>
        <input type="password" name="password"
            placeholder="{{ $user ? 'Password baru (opsional)' : 'Minimal 8 karakter' }}"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('password') border-red-400 bg-red-50 @enderror"
            {{ $user ? '' : 'required' }} autocomplete="new-password">
        @error('password')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Role --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Role <span class="text-red-500">*</span></label>
        <select name="role"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200">
            <option value="user"  {{ old('role', $user?->role ?? 'user') === 'user'  ? 'selected' : '' }}>User</option>
            <option value="admin" {{ old('role', $user?->role ?? 'user') === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        @error('role')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Status --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Status Akun</label>
        <div class="flex gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="is_active" value="1" class="text-indigo-600 focus:ring-indigo-300"
                    {{ old('is_active', $user ? ($user->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }}>
                <span class="text-sm text-slate-700">Aktif</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="is_active" value="0" class="text-indigo-600 focus:ring-indigo-300"
                    {{ old('is_active', $user ? ($user->is_active ? '1' : '0') : '1') == '0' ? 'checked' : '' }}>
                <span class="text-sm text-slate-700">Nonaktif</span>
            </label>
        </div>
    </div>

    {{-- Enrollment Learning Schema --}}
    <div class="space-y-2">
        <label class="block text-xs font-semibold text-slate-700">Enrollment Materi</label>
        <p class="text-xs text-slate-400">Pilih learning schema yang bisa diakses user ini.</p>
        @forelse($allSchemas as $schema)
        @php
            $checked    = in_array($schema->id, old('schema_ids', $enrolledIds ?? []));
            $pivot      = ($enrollments ?? collect())->firstWhere('id', $schema->id)?->pivot;
        @endphp
        <label class="flex items-center gap-3 rounded-xl border px-4 py-3 cursor-pointer transition
            {{ $checked ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
            <input type="checkbox" name="schema_ids[]" value="{{ $schema->id }}"
                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300"
                {{ $checked ? 'checked' : '' }}
                onchange="this.closest('label').className = this.checked
                    ? 'flex items-center gap-3 rounded-xl border px-4 py-3 cursor-pointer transition border-indigo-300 bg-indigo-50'
                    : 'flex items-center gap-3 rounded-xl border px-4 py-3 cursor-pointer transition border-slate-200 bg-white hover:bg-slate-50'">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800">{{ $schema->title }}</p>
                @if($schema->description)
                <p class="text-xs text-slate-400 truncate">{{ $schema->description }}</p>
                @endif
            </div>
            @if($pivot)
            <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold
                {{ $pivot->status === 'completed' ? 'bg-green-100 text-green-600' :
                   ($pivot->status === 'dropped'   ? 'bg-red-100 text-red-600' :
                                                     'bg-indigo-100 text-indigo-600') }}">
                {{ ucfirst($pivot->status) }}
            </span>
            @endif
        </label>
        @empty
        <p class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-400">Belum ada learning schema aktif.</p>
        @endforelse
        @error('schema_ids')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Submit --}}
    <div class="pt-2 flex gap-3">
        <button type="submit"
            class="flex-1 rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-indigo-700 transition">
            {{ $user ? 'Simpan Perubahan' : 'Tambah User' }}
        </button>
        <a href="{{ route('admin.users.index') }}"
           class="flex-1 rounded-full border border-slate-200 bg-slate-50 py-2.5 text-center text-sm font-medium text-slate-600 active:bg-slate-100 transition">
            Batal
        </a>
    </div>
</form>
