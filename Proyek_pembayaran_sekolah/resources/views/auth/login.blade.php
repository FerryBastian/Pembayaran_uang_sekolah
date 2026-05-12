<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | Sistem Pembayaran Sekolah</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body>
    <main class="min-h-screen bg-background">
        <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
            <section class="relative hidden overflow-hidden bg-primary px-12 py-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -left-24 top-16 h-72 w-72 rounded-full border border-white"></div>
                    <div class="absolute bottom-20 right-10 h-96 w-96 rounded-full border border-white"></div>
                    <div class="absolute left-1/3 top-1/3 h-40 w-40 rounded-full border border-white"></div>
                </div>

                <div class="relative z-10 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold leading-6">Sistem Pembayaran</p>
                        <p class="text-sm font-medium text-blue-100">Uang Sekolah</p>
                    </div>
                </div>

                <div class="relative z-10 max-w-xl">
                    <p class="mb-4 inline-flex rounded-full bg-white/15 px-3 py-1 text-sm font-semibold text-blue-50 ring-1 ring-white/20">
                        Portal Web Sekolah
                    </p>
                    <h1 class="text-4xl font-extrabold leading-tight tracking-normal text-white xl:text-5xl">
                        Kelola tagihan, pembayaran, dan laporan sekolah dalam satu sistem.
                    </h1>
                    <p class="mt-5 text-base leading-7 text-blue-100">
                        Akses dashboard sesuai peran untuk admin, guru, siswa, dan orang tua dengan autentikasi web berbasis session.
                    </p>
                </div>

                <div class="relative z-10 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <p class="text-2xl font-bold">4</p>
                        <p class="mt-1 text-xs font-medium text-blue-100">Role pengguna</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <p class="text-2xl font-bold">24/7</p>
                        <p class="mt-1 text-xs font-medium text-blue-100">Akses web</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                        <p class="text-2xl font-bold">PDF</p>
                        <p class="mt-1 text-xs font-medium text-blue-100">Laporan</p>
                    </div>
                </div>
            </section>

            <section class="flex items-center justify-center px-4 py-10 sm:px-6 lg:px-10">
                <div class="w-full max-w-md">
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary text-white">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-secondary">Sistem Pembayaran</p>
                            <p class="text-sm text-slate-500">Uang Sekolah</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-card p-6 shadow-sm sm:p-8">
                        <div>
                            <h2 class="text-2xl font-bold text-secondary">Masuk ke akun</h2>
                            <p class="mt-2 text-sm text-slate-500">
                                Gunakan username dan password yang sudah terdaftar.
                            </p>
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

                        <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5" x-data="{ showPassword: false }">
                            @csrf

                            <div>
                                <label for="username" class="block text-sm font-semibold text-secondary">Username</label>
                                <div class="mt-2">
                                    <input
                                        id="username"
                                        name="username"
                                        type="text"
                                        value="{{ old('username') }}"
                                        autocomplete="username"
                                        autofocus
                                        required
                                        class="block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @error('username') border-danger focus:border-danger focus:ring-danger @enderror"
                                        placeholder="Masukkan username"
                                    >
                                </div>
                                @error('username')
                                    <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-secondary">Password</label>
                                <div class="relative mt-2">
                                    <input
                                        id="password"
                                        name="password"
                                        x-bind:type="showPassword ? 'text' : 'password'"
                                        autocomplete="current-password"
                                        required
                                        class="block w-full rounded-xl border-slate-300 pr-32 text-sm shadow-sm focus:border-primary focus:ring-primary @error('password') border-danger focus:border-danger focus:ring-danger @enderror"
                                        placeholder="Masukkan password"
                                    >
                                    <button
                                        type="button"
                                        class="absolute right-2 top-1/2 inline-flex -translate-y-1/2 items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-primary hover:bg-blue-50"
                                        x-on:click="showPassword = !showPassword"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path x-show="!showPassword" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Zm10 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path x-show="showPassword" x-cloak d="M3 3l18 18M10.6 10.6A3 3 0 0 0 13.4 13.4M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a18.7 18.7 0 0 1-3 4.1M6.6 6.7C3.7 8.7 2 12 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.4-1.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span x-text="showPassword ? 'Sembunyikan' : 'Lihat Password'"></span>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
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
                                class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            >
                                Masuk
                            </button>
                        </form>
                    </div>

                    <p class="mt-6 text-center text-xs text-slate-500">
                        &copy; {{ date('Y') }} Sistem Informasi Pembayaran Uang Sekolah.
                    </p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
