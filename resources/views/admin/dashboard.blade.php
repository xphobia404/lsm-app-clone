<x-admin-layout title="Dashboard">
<div class="px-4 pt-5 pb-10 space-y-6">

    {{-- GREETING --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-800">Selamat datang, {{ auth()->user()->name }} 👋</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 gap-3">

        {{-- Total Users --}}
        <div class="rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-4 text-white shadow-md">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-indigo-100">Total Users</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold">{{ $totalUsers }}</p>
            <div class="mt-2 flex items-center gap-1.5">
                <span class="inline-flex items-center gap-0.5 rounded-full bg-white/20 px-2 py-0.5 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $activeUsers }} aktif
                </span>
                @if($inactiveUsers > 0)
                <span class="inline-flex items-center gap-0.5 rounded-full bg-white/10 px-2 py-0.5 text-xs">
                    {{ $inactiveUsers }} nonaktif
                </span>
                @endif
            </div>
        </div>

        {{-- Total Learning Schema --}}
        <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-violet-700 p-4 text-white shadow-md">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-medium text-violet-100">Learning Schema</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold">{{ $totalSchemas }}</p>
            <p class="mt-2 text-xs text-violet-100">schema aktif</p>
        </div>

        {{-- Total Section --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-slate-500">Total Section</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalSections }}</p>
            <p class="text-xs text-slate-400 mt-0.5">section tersedia</p>
        </div>

        {{-- Total Quiz --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-slate-500">Quiz & Percobaan</p>
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $totalQuizzes }}</p>
            <p class="text-xs text-slate-400 mt-0.5">
                <span class="font-medium text-amber-600">{{ $totalAttempts }}x</span> dicoba
            </p>
        </div>

    </div>

    {{-- USER SELESAI HIGHLIGHT --}}
    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-emerald-800">User Lulus Semua Section</p>
                    <p class="text-xs text-emerald-600">Sudah menyelesaikan seluruh kurikulum</p>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-emerald-700">{{ $completedUsers->count() }}</p>
        </div>
        @if($completedUsers->isNotEmpty())
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach($completedUsers->take(5) as $u)
            <a href="{{ route('admin.users.show', $u) }}"
               class="flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-800 active:bg-emerald-200 transition">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-emerald-600 text-white text-[10px] font-bold">
                    {{ strtoupper(substr($u->name ?: $u->username, 0, 1)) }}
                </span>
                {{ Str::limit($u->name ?: $u->username, 12) }}
            </a>
            @endforeach
            @if($completedUsers->count() > 5)
            <span class="flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-600">
                +{{ $completedUsers->count() - 5 }} lainnya
            </span>
            @endif
        </div>
        @endif
    </div>

    {{-- DONUT CHART --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
            <h3 class="text-sm font-semibold text-slate-700">Status Progress Keseluruhan</h3>
        </div>
        <div id="donutChart"></div>
    </div>

    {{-- BAR CHART: Penyelesaian per Schema --}}
    @if(isset($chartLabels) && $chartLabels->count() > 0)
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <h3 class="text-sm font-semibold text-slate-700">Penyelesaian per Learning Schema</h3>
        </div>
        <div id="barChart"></div>
    </div>
    @endif

    {{-- SCHEMA STATS --}}
    @if(isset($schemaStats) && $schemaStats->isNotEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 class="text-sm font-semibold text-slate-700">Section per Learning Schema</h3>
        </div>
        <div class="space-y-3">
            @php $maxSections = $schemaStats->max('sections_count') ?: 1; @endphp
            @foreach($schemaStats as $schema)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span class="text-xs font-medium text-slate-700">{{ $schema->title }}</span>
                    </div>
                    <span class="text-xs font-bold text-slate-800">{{ $schema->sections_count }}
                        <span class="font-normal text-slate-400">section</span>
                    </span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-indigo-500 transition-all duration-500"
                         style="width: {{ $maxSections > 0 ? round(($schema->sections_count / $maxSections) * 100) : 0 }}%">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- RECENT ACTIVITY --}}
    <div>
        <div class="mb-3 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <h3 class="text-sm font-semibold text-slate-700">Aktivitas Terbaru</h3>
        </div>

        @if($recentProgress->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="mt-2 text-xs text-slate-400">Belum ada aktivitas.</p>
        </div>
        @else
        <div class="space-y-2">
            @foreach($recentProgress as $prog)
            <a href="{{ route('admin.users.show', $prog->user) }}"
               class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm active:bg-slate-50 transition">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($prog->user->name ?: $prog->user->username, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $prog->user->name ?: $prog->user->username }}</p>
                    <p class="text-xs text-slate-400 truncate flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        {{ $prog->section->title ?? '-' }}
                    </p>
                </div>
                <div class="flex-shrink-0 text-right">
                    @if($prog->status === 'completed')
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Selesai
                        </span>
                    @elseif($prog->status === 'in_progress')
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Berjalan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Belum
                        </span>
                    @endif
                    <p class="mt-0.5 text-[10px] text-slate-400">{{ $prog->updated_at->diffForHumans() }}</p>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>
<script>
    var completed  = {{ (int) $donutCompleted }};
    var inProgress = {{ (int) $donutInProgress }};
    var notStarted = {{ (int) $donutNotStarted }};
    var total = completed + inProgress + notStarted;

    new ApexCharts(document.getElementById('donutChart'), {
        chart: { type: 'donut', height: 230, toolbar: { show: false }, animations: { speed: 600 } },
        series: total > 0 ? [completed, inProgress, notStarted] : [1],
        labels: total > 0 ? ['Selesai', 'Sedang Berjalan', 'Belum Mulai'] : ['Belum Ada Data'],
        colors: total > 0 ? ['#10b981', '#6366f1', '#e2e8f0'] : ['#f1f5f9'],
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        total: {
                            show: true, label: 'Total Progress',
                            fontSize: '11px', fontWeight: 600, color: '#94a3b8',
                            formatter: () => total > 0 ? total : '0'
                        },
                        value: { fontSize: '20px', fontWeight: 800, color: '#1e293b' }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontSize: '11px', markers: { size: 8, shape: 'circle' }, itemMargin: { horizontal: 10, vertical: 4 } },
        stroke: { width: 2, colors: ['#fff'] },
        tooltip: { enabled: total > 0 }
    }).render();

    var barEl = document.getElementById('barChart');
    if (barEl) {
        new ApexCharts(barEl, {
            chart: { type: 'bar', height: 210, toolbar: { show: false }, animations: { speed: 600 } },
            series: [{ name: 'User Selesai per Schema', data: {!! json_encode($chartData->values()) !!} }],
            xaxis: {
                categories: {!! json_encode($chartLabels->values()) !!},
                labels: { style: { fontSize: '10px', colors: '#94a3b8' }, rotate: -30, maxHeight: 60 }
            },
            yaxis: { min: 0, tickAmount: 4, labels: { style: { fontSize: '10px', colors: '#94a3b8' } } },
            colors: ['#8b5cf6'],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '52%', dataLabels: { position: 'top' } } },
            dataLabels: { enabled: true, offsetY: -18, style: { fontSize: '10px', colors: ['#475569'] } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            tooltip: { y: { formatter: v => v + ' user selesai' } }
        }).render();
    }
</script>

</x-admin-layout>
