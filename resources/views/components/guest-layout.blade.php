@props([])

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="theme-color" content="#1e3a8a" />
    <title>{{ config('app.name', 'LSM App') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 antialiased">

    {{-- Login Header --}}
    <div class="flex flex-col items-center justify-center pt-12 pb-6 px-4">
        {{-- Logo lingkaran biru --}}
        <div class="flex h-16 w-16 items-center justify-center rounded-full shadow-lg mb-3"
            style="background-color:#1e3a8a;">
            <img src="{{ asset('images/logo.jpg') }}" alt="PT. Javaro Abadi Sejahtera"
                class="h-16 w-16 rounded-full object-cover shadow-lg mb-3">
            </svg>
        </div>
        <p class="text-lg font-black text-slate-800 tracking-widest">PT. JAVARO</p>
        <p class="text-xs text-slate-500 tracking-wide">Abadi Sejahtera · Training &amp; Consulting</p>
    </div>

    <div class="flex justify-center px-4">
        <div class="w-full max-w-sm">
            {{ $slot }}
        </div>
    </div>

</body>

</html>
