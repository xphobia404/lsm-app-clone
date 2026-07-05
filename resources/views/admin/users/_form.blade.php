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
        <input
            type="text" name="name"
            value="{{ old('name', $user?->name) }}"
            placeholder="Masukkan nama lengkap"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('name') border-red-400 bg-red-50 @enderror"
            required
        >
        @error('name')
        <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Username --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Username <span class="text-red-500">*</span></label>
        <input
            type="text" name="username"
            value="{{ old('username', $user?->username) }}"
            placeholder="Contoh: budi123"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('username') border-red-400 bg-red-50 @enderror"
            required
            autocomplete="username"
        >
        @error('username')
        <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password (only required on create) --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">
            Password
            @if($user) <span class="font-normal text-slate-400">(kosongkan jika tidak diganti)</span> @else <span class="text-red-500">*</span> @endif
        </label>
        <input
            type="password" name="password"
            placeholder="{{ $user ? 'Password baru (opsional)' : 'Minimal 6 karakter' }}"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 @error('password') border-red-400 bg-red-50 @enderror"
            {{ $user ? '' : 'required' }}
            autocomplete="new-password"
        >
        @error('password')
        <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Spesialisasi / Course Types --}}
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Spesialisasi</label>
        <p class="text-xs text-slate-400 mb-2">Pilih satu atau lebih spesialisasi untuk user ini.</p>
        <div class="space-y-2">
            @foreach($courseTypes as $ct)
            <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-2.5 cursor-pointer hover:bg-indigo-50 hover:border-indigo-300 transition">
                <input
                    type="checkbox"
                    name="course_type_ids[]"
                    value="{{ $ct->id }}"
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300"
                    {{ in_array($ct->id, old('course_type_ids', $selectedTypeIds ?? [])) ? 'checked' : '' }}
                >
                <div class="flex-1">
                    <span class="text-sm font-medium text-slate-800">{{ $ct->name }}</span>
                    @if($ct->description)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $ct->description }}</p>
                    @endif
                </div>
                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-500">{{ $ct->slug }}</span>
            </label>
            @endforeach
        </div>
        @error('course_type_ids')
        <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status aktif (edit only) --}}
    @if($user)
    <div class="space-y-1">
        <label class="block text-xs font-semibold text-slate-700">Status Akun</label>
        <div class="flex gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="is_active" value="1" class="text-indigo-600 focus:ring-indigo-300" {{ old('is_active', $user->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                <span class="text-sm text-slate-700">Aktif</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="is_active" value="0" class="text-indigo-600 focus:ring-indigo-300" {{ old('is_active', $user->is_active ? '1' : '0') == '0' ? 'checked' : '' }}>
                <span class="text-sm text-slate-700">Nonaktif</span>
            </label>
        </div>
    </div>
    @endif

    {{-- Submit --}}
    <div class="pt-2 flex gap-3">
        <button
            type="submit"
            class="flex-1 rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-indigo-700 transition"
        >
            {{ $user ? 'Simpan Perubahan' : 'Tambah User' }}
        </button>
        <a href="{{ route('admin.users.index') }}"
           class="flex-1 rounded-full border border-slate-200 bg-slate-50 py-2.5 text-center text-sm font-medium text-slate-600 active:bg-slate-100 transition">
            Batal
        </a>
    </div>
</form>
