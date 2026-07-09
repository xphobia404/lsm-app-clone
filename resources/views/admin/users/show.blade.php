<x-admin-layout :title="'Detail: ' . ($user->name ?: $user->username)">
    <div class="px-4 pt-5 pb-10">

        <div class="mb-4 flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}" class="text-xs font-medium text-indigo-600">&larr; Kembali</a>
            <h2 class="text-base font-bold text-slate-800">Detail User</h2>
        </div>

        @if (session('success'))
            <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
        @endif

        {{-- USER CARD --}}
        <div class="mb-5 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-4 text-white shadow-md">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 text-xl font-extrabold">
                    {{ strtoupper(substr($user->name ?: $user->username, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-base font-bold">{{ $user->name ?: $user->username }}</p>
                    <p class="text-xs text-indigo-200">&#64;{{ $user->username }} &bull; {{ $user->email }}</p>
                    <span
                        class="mt-1 inline-block rounded-full px-2 py-0.5 text-[10px] font-semibold
                    {{ $user->role === 'admin' ? 'bg-purple-400/30 text-purple-100' : 'bg-white/20 text-white' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
                <span
                    class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold
                {{ $user->is_active ? 'bg-emerald-400/20 text-emerald-100' : 'bg-red-400/20 text-red-100' }}">
                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">
                <div class="rounded-xl bg-white/10 px-3 py-2 text-center">
                    <p class="text-lg font-bold">{{ $totalSections ?? 0 }}</p>
                    <p class="text-[10px] text-indigo-200">Total Section</p>
                </div>
                <div class="rounded-xl bg-white/10 px-3 py-2 text-center">
                    <p class="text-lg font-bold">{{ $user->progresses_count }}</p>
                    <p class="text-[10px] text-indigo-200">Section Diakses</p>
                </div>
                <div class="rounded-xl bg-white/10 px-3 py-2 text-center">
                    <p class="text-lg font-bold">{{ $completedSections ?? 0 }}</p>
                    <p class="text-[10px] text-indigo-200">Section Selesai</p>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="text-sm font-semibold text-slate-700">Materi Terdaftar</h3>
                </div>
                <a href="{{ route('admin.users.edit', $user) }}" class="text-xs font-medium text-indigo-600">Kelola
                    &rarr;</a>
            </div>

            @if ($user->learningSchemas->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center">
                    <p class="text-xs text-slate-400">Belum terdaftar ke materi apapun.</p>
                    <a href="{{ route('admin.users.edit', $user) }}"
                        class="mt-2 inline-block text-xs text-indigo-600 font-medium">+ Assign Materi</a>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($user->learningSchemas as $schema)
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
                                    <span
                                        class="rounded-full px-2.5 py-1 text-[10px] font-semibold
                            {{ $pivot->status === 'completed'
                                ? 'bg-green-100 text-green-700'
                                : ($pivot->status === 'dropped'
                                    ? 'bg-red-100 text-red-600'
                                    : 'bg-indigo-100 text-indigo-700') }}">
                                        {{ ucfirst($pivot->status) }}
                                    </span>
                                    {{-- Update status --}}
                                    <form method="POST" action="{{ route('admin.users.enrollment.status', $user) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="learning_schema_id" value="{{ $schema->id }}">
                                        <select name="status" onchange="this.form.submit()"
                                            class="rounded-full border border-slate-200 bg-slate-50 px-2 py-1 text-[10px] text-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-300">
                                            <option value="active"
                                                {{ $pivot->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed"
                                                {{ $pivot->status === 'completed' ? 'selected' : '' }}>Completed
                                            </option>
                                            <option value="dropped"
                                                {{ $pivot->status === 'dropped' ? 'selected' : '' }}>Dropped</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SEMUA SECTION USER --}}
        <div class="mb-5">
            <div class="mb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6m-7 4h8m-8 4h8m-8 4h5" />
                </svg>
                <h3 class="text-sm font-semibold text-slate-700">Semua Section User</h3>
            </div>

            @if (empty($sectionsWithStatus) || $sectionsWithStatus->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center">
                    <p class="text-xs text-slate-400">User belum memiliki section dari schema yang terdaftar.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($sectionsWithStatus as $section)
                        @php
                            $statusClass =
                                $section->progress_status === 'completed'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-slate-100 text-slate-500';

                            $statusLabel = $section->progress_status === 'completed' ? 'Completed' : 'Not Completed';
                        @endphp

                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $section->title }}
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        {{ $section->schema_title }}
                                    </p>
                                </div>

                                <span
                                    class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- PROGRESS TERBARU --}}
        <div class="mb-5">
            <div class="mb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <h3 class="text-sm font-semibold text-slate-700">Progress Terbaru</h3>
            </div>

            @if ($user->progresses->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center">
                    <p class="text-xs text-slate-400">Belum ada progres section.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($user->progresses as $progress)
                        @php
                            $badgeClass =
                                ($progress->status ?? null) === 'completed'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-slate-100 text-slate-500';

                            $badgeLabel = ($progress->status ?? null) === 'completed' ? 'Completed' : 'Not Completed';
                        @endphp

                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $progress->section->title ?? 'Section tidak ditemukan' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        {{ $progress->updated_at?->format('d M Y H:i') }}
                                    </p>
                                </div>

                                <span
                                    class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold {{ $badgeClass }}">
                                    {{ $badgeLabel }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>
