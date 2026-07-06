<x-admin-layout :title="'Enrollment: ' . ($user->name ?: $user->username)">
<div class="px-4 pt-5 pb-10 space-y-4">

    <div class="mb-2 flex items-center gap-2">
        <a href="{{ route('admin.users.show', $user) }}" class="text-xs text-indigo-600 font-medium">&larr; Kembali</a>
        <h2 class="text-base font-bold text-slate-800">Kelola Enrollment</h2>
    </div>

    {{-- Info user --}}
    <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 flex items-center gap-3 shadow-sm">
        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 text-sm font-bold">
            {{ strtoupper(substr($user->name ?: $user->username, 0, 1)) }}
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-800">{{ $user->name ?: $user->username }}</p>
            <p class="text-xs text-slate-400">&#64;{{ $user->username }} &bull; {{ $enrollments->count() }} schema terdaftar</p>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    {{-- Form sync --}}
    <form method="POST" action="{{ route('admin.users.enrollment.update', $user) }}">
        @csrf @method('PUT')
        <div class="space-y-2 mb-4">
            @forelse($allSchemas as $schema)
            @php
                $enrolled = in_array($schema->id, $enrolledIds);
                $pivot = $enrollments->firstWhere('id', $schema->id)?->pivot;
            @endphp
            <label class="flex items-center gap-3 rounded-2xl border px-4 py-3 cursor-pointer transition
                {{ $enrolled ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                <input
                    type="checkbox"
                    name="schema_ids[]"
                    value="{{ $schema->id }}"
                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-300"
                    {{ $enrolled ? 'checked' : '' }}
                >
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800">{{ $schema->title }}</p>
                    @if($schema->description)
                    <p class="text-xs text-slate-400 truncate">{{ $schema->description }}</p>
                    @endif
                </div>
                @if($enrolled && $pivot)
                <div class="text-right flex-shrink-0">
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold
                        {{ $pivot->status === 'completed' ? 'bg-green-100 text-green-600' :
                           ($pivot->status === 'dropped'   ? 'bg-red-100 text-red-600'   :
                                                             'bg-indigo-100 text-indigo-600') }}">
                        {{ ucfirst($pivot->status) }}
                    </span>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($pivot->enrolled_at)->format('d M Y') }}</p>
                </div>
                @endif
            </label>
            @empty
            <p class="text-sm text-slate-400 text-center py-8">Belum ada learning schema aktif.</p>
            @endforelse
        </div>

        <button type="submit"
            class="w-full rounded-full bg-indigo-600 py-2.5 text-sm font-semibold text-white shadow-sm active:bg-indigo-700 transition">
            Simpan Enrollment
        </button>
    </form>

</div>
</x-admin-layout>
