<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | Sistem Pembayaran Sekolah</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.jpeg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
        .badge-shield {
            clip-path: polygon(50% 0%, 100% 22%, 100% 78%, 50% 100%, 0% 78%, 0% 22%);
        }
    </style>
</head>
<body>
    <main class="relative min-h-screen overflow-hidden bg-slate-950">

        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute inset-0 scale-105 bg-cover bg-center opacity-55 blur-[1px]"
                style="background-image: url('{{ asset('images/bg_sekolah.jpeg') }}');"
            ></div>
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950/80 via-primary/55 to-sky-200/70"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_25%,rgba(255,255,255,0.32),transparent_34%)]"></div>
        </div>

        <div class="relative z-10 flex min-h-screen items-center justify-center px-4 py-10">
            <div class="w-full max-w-md rounded-2xl border border-white/45 bg-white/90 p-8 shadow-2xl shadow-slate-950/25 backdrop-blur-md sm:p-10">

                <div class="flex flex-col items-center text-center">
                    <div class="badge-shield flex h-24 w-24 items-center justify-center bg-white p-2 shadow-lg ring-1 ring-slate-200">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Logo SMK Swasta GKPI 1" class="h-full w-full object-contain">
                    </div>
                    <h1 class="mt-5 text-2xl font-bold text-secondary">Selamat Datang</h1>
                    <p class="mt-1 text-sm font-medium text-slate-500">SMK Swasta GKPI 1 &mdash; Sistem Pembayaran Sekolah</p>
                </div>

                @if (session('success'))
                    <div class="mt-6 rounded-xl border border-success/20 bg-green-50 p-4 text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mt-6 rounded-xl border border-danger/20 bg-red-50 p-4 text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-4" x-data="{ showPassword: false }">
                    @csrf

                    <div>
                        <div class="flex overflow-hidden rounded-lg border border-slate-300 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary @error('username') border-danger @enderror">
                            <span class="flex w-12 flex-shrink-0 items-center justify-center bg-slate-100 text-slate-500">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M4 20c0-4.4 3.6-7 8-7s8 2.6 8 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                value="{{ old('username') }}"
                                autocomplete="username"
                                autofocus
                                required
                                placeholder="Username"
                                class="block w-full border-0 bg-white text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                            >
                        </div>
                        @error('username')
                            <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex overflow-hidden rounded-lg border border-slate-300 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary @error('password') border-danger @enderror">
                            <span class="flex w-12 flex-shrink-0 items-center justify-center bg-slate-100 text-slate-500">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M8 10.5V8a4 4 0 0 1 8 0v2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input
                                id="password"
                                name="password"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                placeholder="Kata sandi"
                                class="block w-full border-0 bg-white text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0"
                            >
                            <button
                                type="button"
                                class="flex w-12 flex-shrink-0 items-center justify-center border-l border-slate-300 bg-slate-100 text-slate-500 hover:text-slate-700"
                                x-on:click="showPassword = !showPassword"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path x-show="!showPassword" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path x-show="showPassword" x-cloak d="M3 3l18 18M10.6 10.6A3 3 0 0 0 13.4 13.4M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a18.7 18.7 0 0 1-3 4.1M6.6 6.7C3.7 8.7 2 12 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.4-1.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-500">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="rounded border-slate-300 text-primary focus:ring-primary"
                                @checked(old('remember'))
                            >
                            Ingat saya
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-lg bg-primary px-4 py-3 text-sm font-bold uppercase tracking-wide text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                    >
                        Masuk
                    </button>
                </form>

                <!-- <div class="mt-5 flex items-center justify-between text-sm">
                    <a href="#" class="font-medium text-primary hover:underline">Lupa kata sandi?</a>
                    <a href="#" class="font-medium text-orange-500 hover:underline">Hubungi Admin&gt;&gt;</a>
                </div> -->
            </div>
        </div>
    </main>
</body>
</html>
