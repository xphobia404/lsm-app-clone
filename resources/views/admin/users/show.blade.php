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
                <p class="text-xs text-indigo-200">{{ $user->username }}</p>
            </div>
            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-400/20 text-emerald-100' : 'bg-red-400/20 text-red-100' }}">
                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        <p class="mt-3 text-xs text-indigo-200">
            Login terakhir: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
        </p>
    </div>

    {{-- SPESIALISASI --}}
    <div class="mb-5">
        <div class="mb-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <h3 class="text-sm font-semibold text-slate-700">Spesialisasi</h3>
        </div>
        @if($user->courseTypes->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
            <p class="text-xs text-slate-400">Belum ada spesialisasi.</p>
        </div>
        @else
        <div class="flex flex-wrap gap-2">
            @foreach($user->courseTypes as $ct)
            <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700">
                {{ $ct->name }}
            </span>
            @endforeach
        </div>
        @endif
    </div>

    {{-- PROGRESS PER SECTION --}}
    <div class="mb-2 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <h3 class="text-sm font-semibold text-slate-700">Progress per Section</h3>
    </div>

    @if($sections->isEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
        <p class="text-xs text-slate-400">Belum ada section.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($sections as $section)
        @php
            $prog    = $progress[$section->id] ?? null;
            $attempt = $attemptsPerSection[$section->id] ?? null;
            $status  = $prog?->status ?? 'not_started';
        @endphp
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg
                        {{ $status === 'completed' ? 'bg-emerald-100' : ($status === 'in_progress' ? 'bg-blue-100' : 'bg-slate-100') }}">
                        @if($status === 'completed')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif($status === 'in_progress')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                        @endif
                    </div>
                    <p class="text-sm font-semibold text-slate-800 truncate">
                        <span class="text-xs font-normal text-slate-400 mr-1">#{{ $section->order }}</span>{{ $section->title }}
                    </p>
                </div>
                <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium
                    {{ $status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500') }}">
                    {{ $status === 'completed' ? 'Selesai' : ($status === 'in_progress' ? 'Berjalan' : 'Belum') }}
                </span>
            </div>

            @if($attempt)
            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                <div class="rounded-xl bg-slate-50 p-2">
                    <p class="text-lg font-bold text-slate-800">{{ $attempt->total_attempts }}</p>
                    <p class="text-xs text-slate-500">Percobaan</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-2">
                    <p class="text-lg font-bold {{ $attempt->best_score >= 100 ? 'text-emerald-600' : ($attempt->best_score >= 70 ? 'text-amber-600' : 'text-red-500') }}">
                        {{ $attempt->best_score }}%
                    </p>
                    <p class="text-xs text-slate-500">Skor Terbaik</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-2">
                    @if($attempt->ever_passed)
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                    <p class="text-xs text-slate-500 mt-1">Lulus</p>
                </div>
            </div>
            @else
            <p class="mt-2 text-xs text-slate-400 flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Belum ada percobaan quiz.
            </p>
            @endif

            @if($prog?->quiz_passed_at)
            <p class="mt-2 text-xs text-slate-400">Lulus: {{ $prog->quiz_passed_at->format('d M Y, H:i') }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    {{-- QUICK ACTIONS --}}
    <div class="mt-6 flex gap-2">
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
