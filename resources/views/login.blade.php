<x-guest-layout>
    <div class="min-h-screen bg-slate-50 flex flex-col justify-center px-4 py-6">
        <div class="w-full max-w-md mx-auto">
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl overflow-hidden shadow-sm">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo LSM App" class="h-full w-full object-cover">
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Login LMS
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Masuk menggunakan username dan password yang diberikan admin.
                </p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <x-auth-session-status class="mb-4 text-sm font-medium text-green-600" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="username" :value="__('Username')" class="mb-1 text-sm font-medium text-slate-700" />
                        <x-text-input
                            id="username"
                            class="block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"
                            type="text"
                            name="username"
                            :value="old('username')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan username"
                        />
                        <x-input-error :messages="$errors->get('username')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-slate-700" />
                        </div>

                        <x-text-input
                            id="password"
                            class="block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-600 focus:ring-blue-600"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan password"
                        />

                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center gap-2">
                            <input
                                id="remember_me"
                                type="checkbox"
                                class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                name="remember"
                            >
                            <span class="text-sm text-slate-600">Ingat saya</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-full bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:scale-[0.99]"
                    >
                        Masuk
                    </button>
                </form>
            </div>

            <p class="mt-4 text-center text-xs leading-5 text-slate-500">
                Akun tidak dapat dibuat sendiri. Silakan hubungi admin jika belum memiliki akun.
            </p>
        </div>
    </div>
</x-guest-layout>
