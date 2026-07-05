@props(['title' => 'Admin'])

<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover" />
    <meta name="theme-color" content="#1e3a8a" />
    <title>{{ $title }} — {{ config('app.name', 'LSM App') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html {
            scroll-padding-top: 104px;
        }

        body {
            padding-top: 100px;
        }

        nav.bottom-nav {
            padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px));
        }

        .nav-item.active svg,
        .nav-item.active span {
            color: #1d4ed8;
        }

        .nav-item svg,
        .nav-item span {
            color: #94a3b8;
        }

        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="h-full bg-slate-50 antialiased">

    {{-- Top Header (fixed) --}}
    <header class="fixed top-0 left-0 right-0 z-40 bg-white border-b border-slate-200 shadow-sm">
        <div class="flex items-center justify-between px-4 py-2.5">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.jpg') }}" alt="PT. Javaro Abadi Sejahtera"
                    class="h-9 w-9 rounded-full object-cover flex-shrink-0">
                <div class="leading-tight">
                    <p class="text-xs font-black text-slate-800 tracking-wide" style="font-size:11px;">PT. JAVARO</p>
                    <p class="text-slate-500" style="font-size:9px;">Abadi Sejahtera</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">{{ auth()->user()->name ?: auth()->user()->username }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 active:bg-slate-200 transition">Logout</button>
                </form>
            </div>
        </div>
        <div class="border-t border-slate-100 px-4 py-2">
            <h1 class="text-sm font-semibold text-slate-700">{{ $title }}</h1>
        </div>
    </header>

    {{-- Main Content --}}
    <main>
        {{ $slot }}
        <div style="height: calc(80px + env(safe-area-inset-bottom, 0px))"></div>
    </main>

    {{-- Bottom Navigation (fixed) --}}
    <nav class="bottom-nav fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 shadow-lg">
        <div class="flex items-center justify-around px-2 pt-2">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition active:bg-slate-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-xs font-medium">Dashboard</span>
            </a>

            {{-- Spesialisasi --}}
            <a href="{{ route('admin.course-types.index') }}"
                class="nav-item {{ request()->routeIs('admin.course-types.*') ? 'active' : '' }} flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition active:bg-slate-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span class="text-xs font-medium">Spesialisasi</span>
            </a>

            {{-- Section --}}
            <a href="{{ route('admin.sections.index') }}"
                class="nav-item {{ request()->routeIs('admin.sections.*') ? 'active' : '' }} flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition active:bg-slate-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span class="text-xs font-medium">Section</span>
            </a>

            {{-- Users --}}
            <a href="{{ route('admin.users.index') }}"
                class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition active:bg-slate-100">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-xs font-medium">Users</span>
            </a>

        </div>
    </nav>

</body>

</html>
