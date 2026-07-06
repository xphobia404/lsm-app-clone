{{-- resources/views/user/courses.blade.php (semua materi) --}}
<x-app-layout title="Semua Materi">
    <div class="px-4 pt-5 pb-10 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800">Semua Materi</h2>
        </div>

        @forelse($learningSchemas as $schema)
            <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
                {{-- Schema header --}}
                <div class="px-4 py-3 border-b border-slate-50 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-800">{{ $schema->title }}</p>
                        @if($schema->description)
                            <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($schema->description, 80) }}</p>
                        @endif
                    </div>
                    <span class="text-xs text-slate-400">{{ $schema->sections->count() }} section</span>
                </div>

                {{-- Sections list --}}
                @forelse($schema->sections as $section)
                    <a href="{{ route('user.sections.show', [$schema, $section]) }}"
                       class="flex items-center gap-3 px-4 py-3 border-b border-slate-50 last:border-0 active:bg-slate-50 transition">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-50">
                            <span class="text-xs font-bold text-indigo-600">{{ $loop->iteration }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $section->title }}</p>
                        </div>
                        <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @empty
                    <div class="px-4 py-3 text-xs text-slate-400">Belum ada section</div>
                @endforelse
            </div>
        @empty
            <div class="rounded-2xl bg-slate-50 p-8 text-center">
                <p class="text-xs text-slate-400">Belum ada materi tersedia</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
