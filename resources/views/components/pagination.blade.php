@props(['paginator', 'class' => ''])

@if($paginator->hasPages())
<div class="mt-4 {{ $class }}">
    {{-- Info --}}
    <p class="mb-2 text-center text-xs text-slate-400">
        Menampilkan {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }}
        dari {{ $paginator->total() }} data
    </p>

    {{-- Page buttons --}}
    <div class="flex items-center justify-center gap-1.5 flex-wrap">

        {{-- Prev --}}
        @if($paginator->onFirstPage())
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-300 text-xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 text-xs active:bg-slate-50 transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Page numbers --}}
        @php
            $current  = $paginator->currentPage();
            $last     = $paginator->lastPage();
            $window   = 1; // pages shown each side of current
            $pages    = [];
            for ($i = 1; $i <= $last; $i++) {
                if ($i === 1 || $i === $last || ($i >= $current - $window && $i <= $current + $window)) {
                    $pages[] = $i;
                }
            }
        @endphp

        @php $prev = null; @endphp
        @foreach($pages as $page)
            @if($prev !== null && $page - $prev > 1)
                <span class="text-xs text-slate-400 px-0.5">&hellip;</span>
            @endif

            @if($page === $current)
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-xs font-semibold text-white">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-xs text-slate-600 active:bg-slate-50 transition">{{ $page }}</a>
            @endif

            @php $prev = $page; @endphp
        @endforeach

        {{-- Next --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 text-xs active:bg-slate-50 transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-300 text-xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

    </div>
</div>
@endif
