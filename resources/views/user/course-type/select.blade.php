<x-app-layout title="Pilih Spesialisasi">
<div class="px-4 pt-8 pb-10">

    {{-- Header --}}
    <div class="mb-6 text-center">
        <div class="mb-2 text-4xl">🎯</div>
        <h1 class="text-lg font-bold text-slate-800">Pilih Spesialisasi Course</h1>
        <p class="mt-1 text-xs text-slate-500 max-w-xs mx-auto">Pilih spesialisasi yang sesuai. Kamu bisa ganti nanti, tapi progress akan direset.</p>
    </div>

    @if(session('success'))
    <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
    @endif
    @if($errors->any())
    <x-alert type="error" class="mb-4">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </x-alert>
    @endif

    <form method="POST" action="{{ route('user.course-type.store') }}">
        @csrf
        <div class="space-y-3">
            @forelse($courseTypes as $ct)
            <label class="block cursor-pointer">
                <input type="radio" name="course_type_id" value="{{ $ct->id }}"
                       {{ auth()->user()->course_type_id == $ct->id ? 'checked' : '' }}
                       class="peer sr-only" required>
                <div class="flex items-center gap-4 rounded-2xl border-2 border-slate-200 bg-white p-4 transition
                            peer-checked:border-indigo-500 peer-checked:bg-indigo-50">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-2xl">
                        {{ $ct->icon ?? '📚' }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800 text-sm">{{ $ct->name }}</p>
                        @if($ct->description)
                        <p class="text-xs text-slate-500 mt-0.5">{{ $ct->description }}</p>
                        @endif
                        <p class="text-xs text-slate-400 mt-1">📦 {{ $ct->sections_count }} section tersedia</p>
                    </div>
                    {{-- Checkmark --}}
                    <div class="hidden peer-checked:block">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </label>
            @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
                <p class="text-sm text-slate-400">Belum ada spesialisasi tersedia. Hubungi admin.</p>
            </div>
            @endforelse
        </div>

        @if($courseTypes->isNotEmpty())
        <button type="submit"
                class="mt-6 w-full rounded-2xl bg-indigo-600 py-3.5 text-sm font-semibold text-white shadow active:bg-indigo-700 transition">
            Mulai Belajar →
        </button>
        @endif
    </form>

    @if(auth()->user()->hasSelectedCourseType())
    <div class="mt-4 text-center">
        <a href="{{ route('user.dashboard') }}" class="text-xs text-slate-400 underline">Kembali ke dashboard</a>
    </div>
    @endif

</div>
</x-app-layout>
