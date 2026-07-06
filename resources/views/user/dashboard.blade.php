{{-- resources/views/user/dashboard.blade.php --}}
<x-app-layout title="Dashboard">
    <div class="px-4 pt-5 pb-10 space-y-5">
        {{-- Greeting --}}
        <div>
            <h2 class="text-base font-bold text-slate-800">Halo, {{ auth()->user()->name ?? auth()->user()->username }} 👋</h2>
            <p class="text-xs text-slate-500">Selamat datang di Learning Management System</p>
        </div>

        {{-- Quick stats --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-white border border-slate-100 p-4 shadow-sm">
                <p class="text-xs text-slate-500">Materi Selesai</p>
                <p class="mt-1 text-2xl font-black text-indigo-600">{{ $completedCount ?? 0 }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-slate-100 p-4 shadow-sm">
                <p class="text-xs text-slate-500">Total Materi</p>
                <p class="mt-1 text-2xl font-black text-slate-800">{{ $totalCount ?? 0 }}</p>
            </div>
        </div>

        {{-- Learning Schemas --}}
        <div>
            <div class="mb-3 flex items-center justify-between">
                <p class="text-sm font-bold text-slate-800">Materi Tersedia</p>
                <a href="{{ route('user.schemas.index') }}" class="text-xs text-indigo-600 font-medium">Lihat Semua</a>
            </div>
            @forelse($learningSchemas ?? [] as $schema)
                <a href="{{ route('user.schemas.show', $schema) }}"
                   class="mb-2 flex items-center gap-3 rounded-2xl bg-white border border-slate-100 px-4 py-3 shadow-sm active:scale-[0.98] transition block">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 truncate">{{ $schema->title }}</p>
                        <p class="text-xs text-slate-400">{{ $schema->sections->count() }} section</p>
                    </div>
                    <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @empty
                <div class="rounded-2xl bg-slate-50 p-6 text-center">
                    <p class="text-xs text-slate-400">Belum ada materi tersedia</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
