<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover" />
    <meta name="theme-color" content="#0f2460" />
    <title>Login &mdash; PT. Javaro Abadi Sejahtera</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            min-height: 100dvh;
            background: linear-gradient(160deg, #0f2460 0%, #1e3a8a 45%, #1e40af 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 60px rgba(0,0,0,0.25), 0 4px 16px rgba(0,0,0,0.15);
        }
        .input-field {
            width: 100%;
            padding: 12px 16px;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            font-size: 14px;
            color: #1e293b;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .input-field:focus {
            border-color: #1e40af;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(30,64,175,0.12);
        }
        .input-field::placeholder { color: #94a3b8; }
        .btn-login {
            width: 100%;
            padding: 13px;
            border-radius: 999px;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: white;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            box-shadow: 0 4px 16px rgba(30,64,175,0.35);
        }
        .btn-login:active { opacity: 0.9; transform: scale(0.985); }

        /* Decorative circles */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }
    </style>
</head>
<body class="flex flex-col justify-between" style="min-height:100dvh;">

    {{-- Decorative background circles --}}
    <div class="deco-circle" style="width:300px;height:300px;top:-80px;right:-80px;"></div>
    <div class="deco-circle" style="width:200px;height:200px;bottom:80px;left:-60px;"></div>
    <div class="deco-circle" style="width:120px;height:120px;top:40%;right:20px;"></div>

    {{-- Top branding --}}
    <div class="flex flex-col items-center pt-16 pb-8 px-6 relative z-10">

        {{-- Logo --}}
        <div class="mb-5">
            @if(file_exists(public_path('images/logo.jpg')))
                <img src="{{ asset('images/logo.jpg') }}"
                     alt="PT. Javaro Abadi Sejahtera"
                     class="rounded-full object-cover shadow-2xl border-4 border-white/20"
                     style="width:88px;height:88px;">
            @else
                <div class="flex items-center justify-center rounded-full border-4 border-white/20 shadow-2xl"
                     style="width:88px;height:88px;background:#fff;">
                    <svg viewBox="0 0 40 40" width="52" height="52" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <text x="3" y="29" font-family="Georgia,serif" font-size="23" font-weight="bold" fill="#1e3a8a" letter-spacing="-1">JS</text>
                        <line x1="29" y1="7" x2="37" y2="15" stroke="#1e3a8a" stroke-width="1.8" stroke-linecap="round"/>
                        <polygon points="37,15 31,13 33,19" fill="#1e3a8a"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Company name --}}
        <p class="text-white font-black tracking-widest text-center" style="font-size:18px;letter-spacing:3px;">PT. JAVARO</p>
        <p class="text-blue-200 text-center mt-0.5" style="font-size:12px;letter-spacing:1px;">ABADI SEJAHTERA</p>
        <p class="text-blue-300 text-center mt-1" style="font-size:11px;">Training &amp; Consulting</p>

        {{-- Divider --}}
        <div class="mt-5 flex items-center gap-3 w-48">
            <div class="flex-1 h-px bg-white/20"></div>
            <span class="text-white/40 text-xs">LMS</span>
            <div class="flex-1 h-px bg-white/20"></div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="relative z-10 px-5 pb-10">
        <div class="rounded-3xl bg-white card-shadow p-6">

            <h2 class="text-lg font-bold text-slate-800 mb-1">Selamat Datang</h2>
            <p class="text-xs text-slate-500 mb-5">Masuk dengan akun yang diberikan admin</p>

            {{-- Session Status --}}
            @if(session('status'))
                <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-2.5 text-xs font-medium text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error --}}
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-2.5 text-xs text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Username --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5" for="username">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <input id="username" type="text" name="username"
                               value="{{ old('username') }}"
                               class="input-field" style="padding-left:40px;"
                               placeholder="Masukkan username"
                               required autofocus autocomplete="username" />
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5" for="password">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input id="password" type="password" name="password"
                               class="input-field" style="padding-left:40px;padding-right:44px;"
                               placeholder="Masukkan password"
                               required autocomplete="current-password" />
                        {{-- Toggle show/hide password --}}
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 active:text-slate-600">
                            <svg id="eye-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember"
                           class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-xs text-slate-600">Ingat saya</span>
                </label>

                {{-- Submit --}}
                <button type="submit" class="btn-login mt-1">
                    Masuk
                </button>
            </form>
        </div>

        <p class="mt-5 text-center text-xs text-blue-200/70 px-4">
            Akun tidak dapat dibuat sendiri.<br>Hubungi admin jika belum memiliki akun.
        </p>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById('password');
            var icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
    </script>

</body>
</html>
