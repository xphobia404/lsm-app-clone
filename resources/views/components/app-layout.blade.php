@props(['title' => 'LSM App'])

<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover" />
    <meta name="theme-color" content="#0f2460" />
    <title>{{ $title }} — {{ config('app.name', 'LSM App') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-padding-top: 56px;
        }

        body {
            padding-top: 56px;
        }

        nav.bottom-nav {
            padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px));
        }
        .nav-item.active .nav-icon { color: #1d4ed8; }
        .nav-item.active .nav-label { color: #1d4ed8; font-weight: 700; }
        .nav-item .nav-icon { color: #94a3b8; }
        .nav-item .nav-label { color: #94a3b8; }
    </style>
</head>

<body class="h-full bg-slate-50 antialiased">

    {{-- Top Header --}}
    <header class="fixed top-0 left-0 right-0 z-40 flex items-center justify-between bg-white border-b border-slate-200 px-4 py-2.5 shadow-sm">
        <div class="flex items-center gap-2.5">
            @if(file_exists(public_path('images/logo.jpg')))
                <img src="{{ asset('images/logo.jpg') }}" alt="Javaro"
                     class="h-8 w-8 rounded-full object-cover flex-shrink-0">
            @else
                <div class="flex h-8 w-8 items-center justify-center rounded-full flex-shrink-0"
                     style="background-color:#1e3a8a;">
                    <svg viewBox="0 0 40 40" width="22" height="22" fill="none">
                        <text x="4" y="27" font-family="Georgia,serif" font-size="20" font-weight="bold" fill="white" letter-spacing="-1">JS</text>
                    </svg>
                </div>
            @endif
            <div class="leading-tight">
                <p class="font-black text-slate-800 tracking-wide" style="font-size:10px;">PT. JAVARO</p>
                <p class="text-slate-400" style="font-size:8px;">Abadi Sejahtera</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500">{{ auth()->user()->name ?: auth()->user()->username }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 active:bg-slate-200 transition">Keluar</button>
            </form>
        </div>
    </header>

    {{-- Main Content --}}
    <main>
        {{ $slot }}
        <div style="height: calc(72px + env(safe-area-inset-bottom, 0px))"></div>
    </main>

    {{-- Bottom Navigation --}}
    <nav class="bottom-nav fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 shadow-lg">
        <div class="flex items-center justify-around px-2 pt-2">

            {{-- Home --}}
            <a href="{{ route('user.dashboard') }}"
               class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }} flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl transition active:bg-slate-100">
                <svg class="nav-icon h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="nav-label text-xs">Home</span>
            </a>

            {{-- Materi --}}
            <a href="{{ route('user.schemas.index') }}"
               class="nav-item {{ request()->routeIs('user.schemas.*') ? 'active' : '' }} flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl transition active:bg-slate-100">
                <svg class="nav-icon h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="nav-label text-xs">Materi</span>
            </a>

        </div>
    </nav>

</body>

</html>
