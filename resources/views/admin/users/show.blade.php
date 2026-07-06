<x-admin-layout :title="'Detail: ' . ($user->name ?: $user->username)">
<div class="px-4 pt-5 pb-10">

    <div class="mb-4 flex items-center gap-2">
        <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <h2 class="text-base font-bold text-slate-800">Detail User</h2>
    </div>

    @if(session('success'))
        <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
    @endif

    {{-- USER CARD --}}
    <div class="mb-5 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-4 text-white shadow-md">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 text-xl font-extrabold">
                {{ strtoupper(substr($user->name ?: $user->username, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-base truncate">{{ $user->name ?: $user->username }}</p>
                <p class="text-xs text-indigo-200">&#64;{{ $user->username }} &bull; {{ $user->email }}</p>
                <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-[10px] font-semibold
                    {{ $user->role === 'admin' ? 'bg-purple-400/30 text-purple-100' : 'bg-white/20 text-white' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold
                {{ $user->is_active ? 'bg-emerald-400/20 text-emerald-100' : 'bg-red-400/20 text-red-100' }}">
                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        {{-- Stats --}}
        <div class="mt-3 grid grid-cols-2 gap-2">
            <div class="rounded-xl bg-white/10 px-3 py-2 text-center">
                <p class="text-lg font-bold">{{ $user->progresses_count }}</p>
                <p class="text-[10px] text-indigo-200">Section Diakses</p>
            </div>
            <div class="rounded-xl bg-white/10 px-3 py-2 text-center">
                <p class="text-lg font-bold">{{ $user->quiz_attempts_count }}</p>
                <p class="text-[10px] text-indigo-200">Percobaan Quiz</p>
            </div>
        </div>
    </div>

    {{-- ENROLLMENT LEARNING SCHEMAS --}}
    <div class="mb-5">
        <div class="mb-2 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <h3 class="text-sm font-semibold text-slate-700">Materi Terdaftar</h3>
            </div>
            <a href="{{ route('admin.users.edit', $user) }}"
               class="text-xs text-indigo-600 font-medium">Kelola &rarr;</a>
        </div>

        @if($user->learningSchemas->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center">
            <p class="text-xs text-slate-400">Belum terdaftar ke materi apapun.</p>
            <a href="{{ route('admin.users.edit', $user) }}"
               class="mt-2 inline-block text-xs text-indigo-600 font-medium">+ Assign Materi</a>
        </div>
        @else
        <div class="space-y-2">
            @foreach($user->learningSchemas as $schema)
            @php $pivot = $schema->pivot; @endphp
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $schema->title }}</p>
                        <p class="text-xs text-slate-400">
                            Enroll: {{ \Carbon\Carbon::parse($pivot->enrolled_at)->format('d M Y') }}
                            &bull; {{ $schema->sections_count }} section
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold
                            {{ $pivot->status === 'completed' ? 'bg-green-100 text-green-700' :
                               ($pivot->status === 'dropped'   ? 'bg-red-100 text-red-600'   :
                                                                 'bg-indigo-100 text-indigo-700') }}">
                            {{ ucfirst($pivot->status) }}
                        </span>
                        {{-- Update status --}}
                        <form method="POST" action="{{ route('admin.users.enrollment.status', $user) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="learning_schema_id" value="{{ $schema->id }}">
                            <select name="status" onchange="this.form.submit()"
                                class="rounded-full border border-slate-200 bg-slate-50 px-2 py-1 text-[10px] text-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-300">
                                <option value="active"    {{ $pivot->status === 'active'    ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $pivot->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="dropped"   {{ $pivot->status === 'dropped'   ? 'selected' : '' }}>Dropped</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- RECENT PROGRESS --}}
    @if($user->progresses->isNotEmpty())
    <div class="mb-5">
        <div class="mb-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <h3 class="text-sm font-semibold text-slate-700">Progress Terbaru</h3>
        </div>
        <div class="space-y-2">
            @foreach($user->progresses as $prog)
            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-2.5">
                <p class="text-xs font-medium text-slate-700 truncate">{{ $prog->section->title ?? '-' }}</p>
                <span class="ml-2 shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold
                    {{ $prog->status === 'completed' ? 'bg-green-100 text-green-700' :
                       ($prog->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500') }}">
                    {{ ucfirst(str_replace('_', ' ', $prog->status)) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- QUICK ACTIONS --}}
    <div class="mt-4 flex gap-2">
        <a href="{{ route('admin.users.edit', $user) }}"
           class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-full border border-slate-300 px-4 py-2.5 text-xs font-medium text-slate-700 active:bg-slate-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            Edit User
        </a>
        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-full border px-4 py-2.5 text-xs font-medium transition
                {{ $user->is_active ? 'border-yellow-200 bg-yellow-50 text-yellow-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                @if($user->is_active)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Nonaktifkan
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aktifkan
                @endif
            </button>
        </form>
    </div>

</div>
</x-admin-layout>
