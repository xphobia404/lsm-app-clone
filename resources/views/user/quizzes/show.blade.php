{{-- resources/views/user/quizzes/show.blade.php --}}
<x-app-layout title="Quiz {{ $currentIndex + 1 }} - {{ $section->title }}">
<div class="px-4 pt-5 pb-10">

    {{-- Back --}}
    <div class="mb-4">
        <a href="{{ route('user.quizzes.index', [$section->learningSchema, $section]) }}"
           class="inline-flex items-center gap-1 text-xs text-indigo-600 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Quiz
        </a>
    </div>

    {{-- Progress Badge --}}
    <div class="mb-4 flex items-center justify-between">
        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
            Soal {{ $currentIndex + 1 }} dari {{ $total }}
        </span>
        <div class="flex gap-1">
            @if($prev)
            <a href="{{ route('user.quizzes.show', [$section->learningSchema, $section, $prev]) }}" class="rounded-lg border border-slate-200 bg-white p-2 active:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            @endif
            @if($next)
            <a href="{{ route('user.quizzes.show', [$section->learningSchema, $section, $next]) }}" class="rounded-lg border border-slate-200 bg-white p-2 active:bg-slate-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            @else
            <a href="{{ route('user.quizzes.index', [$section->learningSchema, $section]) }}" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white active:bg-indigo-700 transition">
                Kerjakan Quiz
            </a>
            @endif
        </div>
    </div>

    {{-- Quiz Card --}}
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pertanyaan</p>
        </div>
        <div class="p-4 space-y-4">
            <p class="text-sm font-semibold text-slate-800 leading-relaxed">{{ $quiz->question }}</p>

            @if($quiz->activeMedia->count())
            <div class="space-y-2">
                @foreach($quiz->activeMedia as $media)
                @switch($media->media_type)
                @case('image')
                <div class="rounded-xl overflow-hidden border border-slate-100">
                    @if($media->file_path)
                    <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title ?: 'Gambar soal' }}" loading="lazy" class="w-full max-h-64 object-contain bg-slate-50">
                    @elseif($media->url)
                    <img src="{{ $media->url }}" alt="{{ $media->title ?: 'Gambar soal' }}" loading="lazy" class="w-full max-h-64 object-contain bg-slate-50">
                    @endif
                    @if($media->title)<p class="px-3 py-1.5 text-xs text-slate-500 bg-slate-50">{{ $media->title }}</p>@endif
                </div>
                @break
                @case('url')
                @if($media->url)
                <a href="{{ $media->url }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-2.5 transition">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-indigo-700 truncate">{{ $media->title ?: 'Buka referensi' }}</p>
                        <p class="text-[10px] text-indigo-400 truncate">{{ $media->url }}</p>
                    </div>
                </a>
                @endif
                @break
                @endswitch
                @endforeach
            </div>
            @endif

            {{-- Pilihan Jawaban (display only, no form) --}}
            <div class="space-y-2">
                @foreach($quiz->getOptions() as $key => $opt)
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border-2 border-slate-200 text-xs font-bold text-slate-400 uppercase">{{ $key }}</span>
                    <span class="text-sm text-slate-700 leading-snug">{{ $opt }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- CTA to full quiz --}}
    <div class="mt-6">
        <a href="{{ route('user.quizzes.index', [$section->learningSchema, $section]) }}"
           class="block w-full rounded-2xl bg-indigo-600 py-3.5 text-center text-sm font-bold text-white shadow-md active:bg-indigo-700 transition">
            Kerjakan Semua Soal
        </a>
    </div>

</div>
</x-app-layout>
