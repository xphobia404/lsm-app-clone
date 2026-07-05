<x-admin-layout title="Kelola Section">
    <div class="px-4 pt-5 pb-10 space-y-5">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h2 class="text-base font-bold text-slate-800">Kelola Section</h2>
            </div>
            <a href="{{ route('admin.sections.create') }}"
                class="inline-flex items-center gap-1.5 rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm active:bg-indigo-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Section
            </a>
        </div>

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('admin.sections.index') }}" class="flex gap-2 flex-wrap">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari section..."
                class="flex-1 min-w-[140px] rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <select name="course_type_id"
                class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">Semua Spesialisasi</option>
                @foreach ($courseTypes as $ct)
                    <option value="{{ $ct->id }}" {{ request('course_type_id') == $ct->id ? 'selected' : '' }}>
                        {{ $ct->name }}</option>
                @endforeach
            </select>
            <select name="status"
                class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <select name="per_page"
                class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                @foreach ([5, 10, 25, 50] as $n)
                    <option value="{{ $n }}" {{ request('per_page', 10) == $n ? 'selected' : '' }}>
                        {{ $n }} / hal</option>
                @endforeach
            </select>
            <button type="submit"
                class="rounded-full bg-slate-100 px-4 py-2 text-xs font-medium text-slate-600 active:bg-slate-200 transition">Cari</button>
            @if (request()->hasAny(['q', 'course_type_id', 'status', 'per_page']))
                <a href="{{ route('admin.sections.index') }}"
                    class="rounded-full bg-red-50 px-4 py-2 text-xs font-medium text-red-500 active:bg-red-100 transition">Reset</a>
            @endif
        </form>

        @if (session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif
        @if (session('error'))
            <x-alert type="error">{{ session('error') }}</x-alert>
        @endif

        {{-- MODE 1: ada $sections --}}
        @if ($sections)
            <p class="text-xs text-slate-400">
                Menampilkan {{ $sections->firstItem() }}–{{ $sections->lastItem() }} dari {{ $sections->total() }} section
                @if (!empty($selectedCourseType))
                    untuk spesialisasi {{ $selectedCourseType->name }}
                @endif
            </p>

            @if ($sections->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">Belum ada section</p>
                    <p class="text-xs text-slate-400 mt-1">Tambahkan section pertama untuk spesialisasi ini.</p>
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach ($sections as $section)
                        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                            <div class="flex items-start gap-3 px-4 py-3">

                                {{-- Thumbnail / order --}}
                                <div class="flex-shrink-0">
                                    @if ($section->thumbnail_url)
                                        <img src="{{ $section->thumbnail_url }}" alt="{{ $section->title }}"
                                            class="h-14 w-20 rounded-xl object-cover" loading="lazy">
                                    @else
                                        <div class="flex h-14 w-20 items-center justify-center rounded-xl"
                                            style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);">
                                            <span class="text-white text-xs font-bold">#{{ $section->order }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 mb-0.5 flex-wrap">
                                        <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-600">#{{ $section->order }}</span>
                                        @if ($section->is_published)
                                            <span class="rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-medium text-green-600">Published</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Draft</span>
                                        @endif
                                        @if ($section->courseType)
                                            <span class="rounded-full bg-violet-50 px-2 py-0.5 text-[10px] font-medium text-violet-600">{{ $section->courseType->name }}</span>
                                        @endif
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 leading-tight">{{ $section->title }}</h3>
                                    @if ($section->description)
                                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $section->description }}</p>
                                    @endif
                                    <div class="mt-1 flex items-center gap-3 text-xs text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $section->quizzes_count }} quiz
                                        </span>
                                        @if ($section->video_url)
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Video
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions: Show (only Draft), Edit, Quiz, Hapus --}}
                                <div class="flex flex-col gap-1.5 flex-shrink-0">

                                    {{-- Show — hanya muncul kalau Draft --}}
                                    @unless ($section->is_published)
                                        <form method="POST" action="{{ route('admin.sections.toggle-publish', $section) }}">
                                            @csrf
                                            <button type="submit" title="Publish section"
                                                class="flex h-8 w-8 items-center justify-center rounded-full border border-green-200 bg-green-50 text-green-600 active:bg-green-100 transition">
                                                {{-- Eye icon --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endunless

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.sections.edit', $section) }}" title="Edit section"
                                        class="flex h-8 w-8 items-center justify-center rounded-full border border-indigo-200 bg-indigo-50 text-indigo-600 active:bg-indigo-100 transition">
                                        {{-- Pencil icon --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    {{-- Quiz --}}
                                    <a href="{{ route('admin.sections.quizzes.index', $section) }}" title="Kelola Quiz"
                                        class="flex h-8 w-8 items-center justify-center rounded-full border border-violet-200 bg-violet-50 text-violet-600 active:bg-violet-100 transition">
                                        {{-- Question mark / academic cap icon --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </a>

                                    {{-- Hapus --}}
                                    <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('Hapus section ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus section"
                                            class="flex h-8 w-8 items-center justify-center rounded-full border border-red-200 bg-red-50 text-red-500 active:bg-red-100 transition">
                                            {{-- Trash icon --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($sections->hasPages())
                    <div class="pt-2">
                        {{ $sections->links() }}
                    </div>
                @endif
            @endif

        {{-- MODE 2: grouped --}}
        @else
            @if ($grouped && $grouped->isNotEmpty())
                @foreach ($grouped as $spesialisasi => $items)
                    <div x-data="{ open: true }"
                        class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <button type="button" @click="open = !open"
                            class="flex w-full items-center justify-between px-4 py-3 bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-slate-100 transition">
                            <div class="flex items-center gap-2">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-600 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </span>
                                <span class="text-sm font-bold text-slate-800">{{ $spesialisasi }}</span>
                                <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-bold text-indigo-600">{{ $items->count() }} section</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-slate-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition class="divide-y divide-slate-100">
                            @foreach ($items as $section)
                                <div class="flex items-start gap-3 px-4 py-3">
                                    {{-- Thumbnail / order badge --}}
                                    <div class="flex-shrink-0">
                                        @if ($section->thumbnail_url)
                                            <img src="{{ $section->thumbnail_url }}" alt="{{ $section->title }}"
                                                class="h-14 w-20 rounded-xl object-cover" loading="lazy">
                                        @else
                                            <div class="flex h-14 w-20 items-center justify-center rounded-xl"
                                                style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);">
                                                <span class="text-white text-xs font-bold">#{{ $section->order }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 mb-0.5">
                                            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-600">#{{ $section->order }}</span>
                                            @if ($section->is_published)
                                                <span class="rounded-full bg-green-50 px-2 py-0.5 text-[10px] font-medium text-green-600">Published</span>
                                            @else
                                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-500">Draft</span>
                                            @endif
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-800 leading-tight truncate">{{ $section->title }}</h3>
                                        @if ($section->description)
                                            <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $section->description }}</p>
                                        @endif
                                        <div class="mt-1 flex items-center gap-3 text-xs text-slate-400">
                                            <span class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $section->quizzes_count }} quiz
                                            </span>
                                            @if ($section->video_url)
                                                <span class="flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Video
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Actions: Show (only Draft), Edit, Quiz, Hapus --}}
                                    <div class="flex flex-col gap-1.5 flex-shrink-0">

                                        {{-- Show — hanya muncul kalau Draft --}}
                                        @unless ($section->is_published)
                                            <form method="POST" action="{{ route('admin.sections.toggle-publish', $section) }}">
                                                @csrf
                                                <button type="submit" title="Publish section"
                                                    class="flex h-8 w-8 items-center justify-center rounded-full border border-green-200 bg-green-50 text-green-600 active:bg-green-100 transition">
                                                    {{-- Eye icon --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endunless

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.sections.edit', $section) }}" title="Edit section"
                                            class="flex h-8 w-8 items-center justify-center rounded-full border border-indigo-200 bg-indigo-50 text-indigo-600 active:bg-indigo-100 transition">
                                            {{-- Pencil icon --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        {{-- Quiz --}}
                                        <a href="{{ route('admin.sections.quizzes.index', $section) }}" title="Kelola Quiz"
                                            class="flex h-8 w-8 items-center justify-center rounded-full border border-violet-200 bg-violet-50 text-violet-600 active:bg-violet-100 transition">
                                            {{-- Clipboard check icon --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                        </a>

                                        {{-- Hapus --}}
                                        <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('Hapus section ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Hapus section"
                                                class="flex h-8 w-8 items-center justify-center rounded-full border border-red-200 bg-red-50 text-red-500 active:bg-red-100 transition">
                                                {{-- Trash icon --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">Belum ada section</p>
                    <p class="text-xs text-slate-400 mt-1">Tambahkan section pertama untuk memulai.</p>
                </div>
            @endif
        @endif

    </div>
</x-admin-layout>
