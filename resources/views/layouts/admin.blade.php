<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — {{ $title ?? 'LSM App' }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Alpine.js: required for x-data / x-show / x-transition directives --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-100 text-slate-900 antialiased">

    <header class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
        <div class="flex items-center justify-between px-4 h-14">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo LSM App" class="h-8 w-8 rounded-xl object-cover">
                <span class="font-semibold text-sm text-slate-800">Admin Panel</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-red-500 font-medium active:opacity-70">Keluar</button>
                </form>
            </div>
        </div>

        {{-- Bottom Nav Admin --}}
        <nav class="flex border-t border-slate-100 bg-white">
            <a href="{{ route('admin.dashboard') }}"
               class="flex-1 flex flex-col items-center gap-0.5 py-2 text-xs {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 font-semibold' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.course-types.index') }}"
               class="flex-1 flex flex-col items-center gap-0.5 py-2 text-xs {{ request()->routeIs('admin.course-types.*') ? 'text-indigo-600 font-semibold' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Spesialisasi
            </a>
            <a href="{{ route('admin.sections.index') }}"
               class="flex-1 flex flex-col items-center gap-0.5 py-2 text-xs {{ request()->routeIs('admin.sections.*') ? 'text-indigo-600 font-semibold' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Section
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="flex-1 flex flex-col items-center gap-0.5 py-2 text-xs {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 font-semibold' : 'text-slate-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Users
            </a>
        </nav>
    </header>

    <main class="min-h-[calc(100dvh-7rem)] pb-8">
        {{ $slot }}
    </main>

</body>
</html>
