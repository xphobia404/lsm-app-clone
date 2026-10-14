<x-admin-layout title="Hasil Kuis User">
<div x-data="{
        showModal: false,
        modalData: {
            user: '',
            email: '',
            section: '',
            score: '',
            passed: false,
            answers: []
        }
     }"
     class="px-4 pt-5 pb-10 space-y-5">

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
                Riwayat percobaan dan nilai kuis yang dikerjakan oleh seluruh user, lengkap dengan detail jawaban yang dipilih dan kunci aktualnya.
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
                        <th class="py-3 px-4 text-center">Rincian Jawaban</th>
                        <th class="py-3 px-4 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($attempts as $attempt)
                    @php
                        $scorePct = $attempt->getScorePercentage();
                        $passingScore = $attempt->passing_score ?? ($attempt->section?->passing_score ?? 70);
                        $passed = $attempt->isPassed();
                        $answers = is_array($attempt->answers) ? $attempt->answers : [];
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
                        <td class="py-3 px-4 text-center">
                            @if(! empty($answers))
                                <button type="button"
                                        @click="modalData = {{ json_encode([
                                            'user' => $attempt->user?->name ?? 'User dihapus',
                                            'email' => $attempt->user?->email ?? '-',
                                            'section' => $attempt->section?->title ?? '-',
                                            'score' => $scorePct.'%',
                                            'passed' => $passed,
                                            'answers' => $answers,
                                        ]) }}; showModal = true"
                                        class="inline-flex items-center gap-1 rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    User vs Actual
                                </button>
                            @else
                                <span class="text-slate-400 text-[11px] italic">Tidak tersimpan</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right text-slate-400 text-[11px] whitespace-nowrap">
                            {{ $attempt->attempted_at ? $attempt->attempted_at->format('d M Y, H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
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

    {{-- Modal Detail Jawaban User vs Actual --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
         style="display: none;">
        <div @click.away="showModal = false"
             class="w-full max-w-2xl max-h-[85vh] flex flex-col rounded-2xl bg-white shadow-2xl overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Detail Jawaban Kuis User</h3>
                    <p class="text-xs text-slate-400">
                        <span x-text="modalData.user" class="font-semibold text-slate-700"></span> &bull; 
                        Section: <span x-text="modalData.section" class="font-medium text-indigo-600"></span> &bull; 
                        Skor: <span x-text="modalData.score" class="font-bold text-slate-800"></span>
                    </p>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            {{-- Modal Content List --}}
            <div class="p-5 overflow-y-auto space-y-3.5 flex-1 divide-y divide-slate-100">
                <template x-for="(ans, index) in modalData.answers" :key="index">
                    <div class="pt-3.5 first:pt-0 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs font-bold text-slate-800">
                                <span class="text-indigo-600" x-text="'Soal ' + (ans.quiz_number || (index + 1)) + ':'"></span>
                                <span x-text="ans.question" class="ml-1 text-slate-700 font-medium"></span>
                            </p>
                            <span :class="ans.is_correct ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
                                  class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full border">
                                <span x-text="ans.is_correct ? 'BENAR ✓' : 'SALAH ✕'"></span>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            {{-- Jawaban User --}}
                            <div :class="ans.is_correct ? 'bg-emerald-50/60 border-emerald-100' : 'bg-rose-50/60 border-rose-100'"
                                 class="p-2.5 rounded-xl border">
                                <p class="text-[10px] uppercase font-bold text-slate-400">Jawaban User:</p>
                                <p class="font-semibold mt-0.5" :class="ans.is_correct ? 'text-emerald-800' : 'text-rose-800'">
                                    <span class="inline-block px-1.5 py-0.2 bg-white rounded border border-slate-200 uppercase font-mono mr-1" x-text="ans.user_answer || '-'"></span>
                                    <span x-text="ans.user_answer_text || '(Tidak ada teks)'"></span>
                                </p>
                            </div>

                            {{-- Kunci Jawaban Sebenarnya --}}
                            <div class="bg-indigo-50/60 border border-indigo-100 p-2.5 rounded-xl">
                                <p class="text-[10px] uppercase font-bold text-indigo-400">Kunci Jawaban Sebenarnya:</p>
                                <p class="font-semibold text-indigo-900 mt-0.5">
                                    <span class="inline-block px-1.5 py-0.2 bg-white rounded border border-indigo-200 uppercase font-mono mr-1 text-indigo-600" x-text="ans.correct_answer || '-'"></span>
                                    <span x-text="ans.correct_answer_text || '(Tidak ada teks)'"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex justify-end">
                <button type="button" @click="showModal = false" class="rounded-full bg-slate-800 px-5 py-2 text-xs font-semibold text-white hover:bg-slate-900 transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
</x-admin-layout>
