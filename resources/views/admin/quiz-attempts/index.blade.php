<x-admin-layout title="Hasil Kuis User">
<div class="px-4 pt-5 pb-10 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-800 font-semibold">Hasil Kuis User</span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-slate-800">Hasil Kuis User</h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Riwayat percobaan dan nilai kuis yang dikerjakan oleh seluruh user.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.quiz-attempts.export', request()->all()) }}"
               class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 shadow-sm hover:bg-emerald-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV {{ request()->hasAny(['search', 'section_id', 'status']) ? '(Hasil Filter)' : 'Semua' }}
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 font-medium">Total Percobaan</p>
                <p class="text-xl font-extrabold text-slate-800 mt-0.5">{{ number_format($totalAttempts) }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-emerald-600 font-medium">Lulus</p>
                <p class="text-xl font-extrabold text-emerald-700 mt-0.5">{{ number_format($totalPassed) }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>

        <div class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-rose-600 font-medium">Belum Lulus</p>
                <p class="text-xl font-extrabold text-rose-700 mt-0.5">{{ number_format($totalFailed) }}</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600 font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.quiz-attempts.index') }}" class="flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[200px]">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, username, atau email user..."
                   class="w-full rounded-full border border-slate-200 bg-white py-2 pl-8 pr-4 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
        </div>

        <select name="section_id" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400 max-w-[220px]">
            <option value="">Semua Section</option>
            @foreach($sections as $s)
                <option value="{{ $s->id }}" {{ (string) request('section_id') === (string) $s->id ? 'selected' : '' }}>
                    {{ $s->title }}
                </option>
            @endforeach
        </select>

        <select name="status" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
            <option value="">Semua Status</option>
            <option value="passed" {{ request('status') === 'passed' ? 'selected' : '' }}>Lulus</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Belum Lulus</option>
        </select>

        <select name="per_page" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400">
            <option value="10" {{ (string) request('per_page', $perPage ?? 25) === '10' ? 'selected' : '' }}>10 / hal</option>
            <option value="15" {{ (string) request('per_page', $perPage ?? 25) === '15' ? 'selected' : '' }}>15 / hal</option>
            <option value="25" {{ (string) request('per_page', $perPage ?? 25) === '25' ? 'selected' : '' }}>25 / hal</option>
            <option value="50" {{ (string) request('per_page', $perPage ?? 25) === '50' ? 'selected' : '' }}>50 / hal</option>
            <option value="100" {{ (string) request('per_page', $perPage ?? 25) === '100' ? 'selected' : '' }}>100 / hal</option>
        </select>

        <button type="submit" class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 transition">
            Filter
        </button>

        @if(request()->hasAny(['search', 'section_id', 'status', 'per_page']))
            <a href="{{ route('admin.quiz-attempts.index') }}"
               class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-500 hover:bg-slate-50 transition">
                Reset
            </a>
        @endif
    </form>

    {{-- Result Count --}}
    @if($attempts->total())
        <p class="text-xs text-slate-400">
            Menampilkan {{ $attempts->firstItem() }}–{{ $attempts->lastItem() }} dari {{ $attempts->total() }} percobaan kuis
        </p>
    @endif

    {{-- Attempts Table --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Section / Materi</th>
                        <th class="py-3 px-4 text-center">Soal / Benar</th>
                        <th class="py-3 px-4 text-center">Skor</th>
                        <th class="py-3 px-4 text-center">Min. Skor</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($attempts as $attempt)
                    @php
                        $scorePct = $attempt->getScorePercentage();
                        $passingScore = $attempt->passing_score ?? ($attempt->section?->passing_score ?? 70);
                        $passed = $attempt->isPassed();
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-4">
                            @if($attempt->user)
                                <a href="{{ route('admin.users.show', $attempt->user) }}" class="font-semibold text-slate-800 hover:text-indigo-600 block">
                                    {{ $attempt->user->name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $attempt->user->email }}</span>
                            @else
                                <span class="text-slate-400 italic">User terhapus</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($attempt->section)
                                <a href="{{ route('admin.sections.quizzes.index', $attempt->section) }}" class="font-medium text-indigo-600 hover:underline block">
                                    {{ $attempt->section->title }}
                                </a>
                                <span class="text-[11px] text-slate-400">
                                    {{ $attempt->section->learningSchemas->pluck('title')->join(', ') ?: '-' }}
                                </span>
                            @else
                                <span class="text-slate-400 italic">Section terhapus</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-medium">
                            <span class="text-slate-800 font-bold">{{ $attempt->correct_answers }}</span> / {{ $attempt->total_questions }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $scorePct }}%
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-medium text-slate-500">
                            {{ $passingScore }}%
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($passed)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Lulus
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-600">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Belum Lulus
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right text-slate-400 text-[11px] whitespace-nowrap">
                            {{ $attempt->attempted_at ? $attempt->attempted_at->format('d M Y, H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <p class="mt-2 text-sm font-medium">Belum ada hasil kuis yang tercatat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($attempts->hasPages())
        <div>{{ $attempts->links() }}</div>
    @endif

</div>
</x-admin-layout>
