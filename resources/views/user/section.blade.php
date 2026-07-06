{{-- resources/views/user/section.blade.php --}}
<x-app-layout :title="$section->title">
    <div class="px-4 pt-5 pb-10">
        {{-- Breadcrumb --}}
        <div class="mb-4 flex items-center gap-2 text-xs">
            <a href="{{ route('user.dashboard') }}" class="text-slate-400">Dashboard</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('user.schemas.index') }}" class="text-slate-400">Materi</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600 font-medium">{{ $section->title }}</span>
        </div>

        {{-- Section Header --}}
        <div class="mb-5">
            <h2 class="text-base font-bold text-slate-800">{{ $section->title }}</h2>
            @if($section->description)
                <p class="mt-1 text-xs text-slate-500">{{ $section->description }}</p>
            @endif
        </div>

        {{-- Contents --}}
        @if($section->contents->isNotEmpty())
            <div class="space-y-3 mb-6">
                @foreach($section->contents as $content)
                    <a href="{{ route('user.contents.show', [$section->learningSchema, $section, $content]) }}"
                       class="flex items-center gap-3 rounded-2xl bg-white border border-slate-100 px-4 py-3 shadow-sm active:scale-[0.98] transition">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $content->title }}</p>
                        </div>
                        <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Quiz CTA --}}
        @if($section->quizzes->isNotEmpty())
            <a href="{{ route('user.quizzes.show', [$section->learningSchema, $section]) }}"
               class="flex items-center justify-between rounded-2xl bg-indigo-600 px-5 py-4 text-white shadow active:bg-indigo-700 transition">
                <div>
                    <p class="text-sm font-bold">Kerjakan Quiz</p>
                    <p class="text-xs text-indigo-200">{{ $section->quizzes->count() }} soal &bull; Passing score {{ $section->passing_score ?? 70 }}%</p>
                </div>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endif
    </div>
</x-app-layout>
