@php
    use Illuminate\Support\Facades\Route;

    $user = auth()->user();
    $role = $user?->role;
    $displayRole = [
        'admin' => 'Admin',
        'guru' => 'Guru',
        'siswa' => 'Siswa',
        'orang_tua' => 'Orang Tua',
    ][$role] ?? 'Guest';

    $pageTitle = trim(($title ?? $__env->yieldContent('title') ?? '') ?: 'Sistem Pembayaran Sekolah');
    $notificationCount = $user
        ? $user->notifikasis()->where('status', 'belum_dibaca')->count()
        : 0;

    $safeRoute = fn (string $name, string $fallback) => Route::has($name) ? route($name) : url($fallback);

    $menus = [
        'admin' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'pattern' => 'admin/dashboard*', 'url' => $safeRoute('admin.dashboard', '/admin/dashboard')],
            ['label' => 'Kelas', 'icon' => 'kelas', 'pattern' => 'admin/kelas*', 'url' => $safeRoute('admin.kelas.index', '/admin/kelas')],
            ['label' => 'Guru', 'icon' => 'guru', 'pattern' => 'admin/guru*', 'url' => $safeRoute('admin.guru.index', '/admin/guru')],
            ['label' => 'Orang Tua', 'icon' => 'orang-tua', 'pattern' => 'admin/orang-tua*', 'url' => $safeRoute('admin.orang-tua.index', '/admin/orang-tua')],
            ['label' => 'Siswa', 'icon' => 'siswa', 'pattern' => 'admin/siswa*', 'url' => $safeRoute('admin.siswa.index', '/admin/siswa')],
            ['label' => 'Tagihan', 'icon' => 'tagihan', 'pattern' => 'admin/tagihan*', 'url' => $safeRoute('admin.tagihan.index', '/admin/tagihan')],
            ['label' => 'Pembayaran', 'icon' => 'pembayaran', 'pattern' => 'admin/pembayaran*', 'url' => $safeRoute('admin.pembayaran.index', '/admin/pembayaran')],
            ['label' => 'Laporan', 'icon' => 'laporan', 'pattern' => 'admin/laporan*', 'url' => $safeRoute('admin.laporan.index', '/admin/laporan')],
            ['label' => 'Notifikasi', 'icon' => 'notifikasi', 'pattern' => 'admin/notifikasi*', 'url' => $safeRoute('admin.notifikasi.index', '/admin/notifikasi')],
            ['label' => 'Profil', 'icon' => 'profil', 'pattern' => 'admin/profil*', 'url' => $safeRoute('admin.profil.edit', '/admin/profil')],
        ],
        'guru' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'pattern' => 'guru/dashboard*', 'url' => $safeRoute('guru.dashboard', '/guru/dashboard')],
            ['label' => 'Data Siswa', 'icon' => 'siswa', 'pattern' => 'guru/siswa*', 'url' => $safeRoute('guru.siswa.index', '/guru/siswa')],
            ['label' => 'Tagihan', 'icon' => 'tagihan', 'pattern' => 'guru/tagihan*', 'url' => $safeRoute('guru.tagihan.index', '/guru/tagihan')],
            ['label' => 'Notifikasi', 'icon' => 'notifikasi', 'pattern' => 'guru/notifikasi*', 'url' => $safeRoute('guru.notifikasi.index', '/guru/notifikasi')],
            ['label' => 'Profil', 'icon' => 'profil', 'pattern' => 'guru/profil*', 'url' => $safeRoute('guru.profil.edit', '/guru/profil')],
        ],
        'orang_tua' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'pattern' => 'orang-tua/dashboard*', 'url' => $safeRoute('orang-tua.dashboard', '/orang-tua/dashboard')],
            ['label' => 'Tagihan Anak', 'icon' => 'tagihan', 'pattern' => 'orang-tua/tagihan*', 'url' => $safeRoute('orang-tua.tagihan.index', '/orang-tua/tagihan')],
            ['label' => 'Pembayaran', 'icon' => 'pembayaran', 'pattern' => 'orang-tua/pembayaran*', 'url' => $safeRoute('orang-tua.pembayaran.index', '/orang-tua/pembayaran')],
            ['label' => 'Notifikasi', 'icon' => 'notifikasi', 'pattern' => 'orang-tua/notifikasi*', 'url' => $safeRoute('orang-tua.notifikasi.index', '/orang-tua/notifikasi')],
            ['label' => 'Profil', 'icon' => 'profil', 'pattern' => 'orang-tua/profil*', 'url' => $safeRoute('orang-tua.profil.edit', '/orang-tua/profil')],
        ],
        'siswa' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'pattern' => 'siswa/dashboard*', 'url' => $safeRoute('siswa.dashboard', '/siswa/dashboard')],
            ['label' => 'Tagihan', 'icon' => 'tagihan', 'pattern' => 'siswa/tagihan*', 'url' => $safeRoute('siswa.tagihan.index', '/siswa/tagihan')],
            ['label' => 'Pembayaran', 'icon' => 'pembayaran', 'pattern' => 'siswa/pembayaran*', 'url' => $safeRoute('siswa.pembayaran.index', '/siswa/pembayaran')],
            ['label' => 'Notifikasi', 'icon' => 'notifikasi', 'pattern' => 'siswa/notifikasi*', 'url' => $safeRoute('siswa.notifikasi.index', '/siswa/notifikasi')],
            ['label' => 'Profil', 'icon' => 'profil', 'pattern' => 'siswa/profil*', 'url' => $safeRoute('siswa.profil.edit', '/siswa/profil')],
        ],
    ];

    $navigation = $menus[$role] ?? [];
    $profileUrl = match ($role) {
        'admin' => $safeRoute('admin.profil.edit', '/admin/profil'),
        'guru' => $safeRoute('guru.profil.edit', '/guru/profil'),
        'orang_tua' => $safeRoute('orang-tua.profil.edit', '/orang-tua/profil'),
        'siswa' => $safeRoute('siswa.profil.edit', '/siswa/profil'),
        default => '#',
    };
    $notificationUrl = match ($role) {
        'admin' => $safeRoute('admin.notifikasi.index', '/admin/notifikasi'),
        'guru' => $safeRoute('guru.notifikasi.index', '/guru/notifikasi'),
        'orang_tua' => $safeRoute('orang-tua.notifikasi.index', '/orang-tua/notifikasi'),
        'siswa' => $safeRoute('siswa.notifikasi.index', '/siswa/notifikasi'),
        default => '#',
    };
    $notificationCountUrl = match ($role) {
        'admin' => $safeRoute('admin.notifikasi.count', '/admin/notifikasi/count'),
        'guru' => $safeRoute('guru.notifikasi.count', '/guru/notifikasi/count'),
        'orang_tua' => $safeRoute('orang-tua.notifikasi.count', '/orang-tua/notifikasi/count'),
        'siswa' => $safeRoute('siswa.notifikasi.count', '/siswa/notifikasi/count'),
        default => null,
    };
    $logoutUrl = Route::has('logout') ? route('logout') : url('/logout');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>

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
    </style>
</head>
<body>
    <div
        x-data="{
            sidebarOpen: false,
            profileOpen: false,
            notificationCount: {{ (int) $notificationCount }},
            notificationCountUrl: @js($notificationCountUrl),
            async refreshNotificationCount() {
                if (!this.notificationCountUrl || !window.axios) return;

                try {
                    const response = await window.axios.get(this.notificationCountUrl);
                    this.notificationCount = Number(response.data.count || 0);
                } catch (error) {
                    // Keep the last known count if the polling request fails.
                }
            },
            init() {
                this.refreshNotificationCount();
                setInterval(() => this.refreshNotificationCount(), 30000);
            }
        }"
        class="min-h-screen bg-background"
    >
        <div
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-30 bg-slate-950/50 lg:hidden"
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-primary text-white shadow-xl transition-transform duration-200 ease-out lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
            aria-label="Sidebar navigasi"
        >
            <div class="flex h-16 items-center justify-between border-b border-white/10 px-6">
                <a href="{{ $role ? url('/' . str_replace('_', '-', $role) . '/dashboard') : url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-10 w-10 object-contain">
                    <span>
                        <span class="block text-sm font-bold leading-5">Pembayaran</span>
                        <span class="block text-xs font-medium text-blue-100">Uang Sekolah</span>
                    </span>
                </a>

                <button
                    type="button"
                    class="rounded-lg p-2 text-blue-100 hover:bg-white/10 hover:text-white lg:hidden"
                    @click="sidebarOpen = false"
                    aria-label="Tutup sidebar"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
                @forelse ($navigation as $item)
                    @php
                        $isActive = request()->is($item['pattern']);
                    @endphp

                    <a
                        href="{{ $item['url'] }}"
                        class="{{ $isActive ? 'bg-white/15 text-white shadow-sm' : 'text-blue-50 hover:bg-white/10 hover:text-white' }} group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition"
                        @click="sidebarOpen = false"
                        aria-current="{{ $isActive ? 'page' : 'false' }}"
                    >
                        <span class="{{ $isActive ? 'text-white' : 'text-blue-100 group-hover:text-white' }} flex h-5 w-5 shrink-0 items-center justify-center">
                            @switch($item['icon'])
                                @case('dashboard')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 13h7V4H4v9Zm0 7h7v-4H4v4Zm10 0h6v-9h-6v9Zm0-12h6V4h-6v4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @case('kelas')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 6h16M5 6v12h14V6M8 10h8M8 14h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @case('guru')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 7a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    @break
                                @case('orang-tua')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm8 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3 20a5 5 0 0 1 10 0m-1.5-3.5A5 5 0 0 1 21 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    @break
                                @case('siswa')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="m12 4 9 5-9 5-9-5 9-5Zm-5 8v4c0 1.7 2.2 3 5 3s5-1.3 5-3v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @case('tagihan')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M7 3h10a2 2 0 0 1 2 2v16l-3-2-2 2-2-2-2 2-2-2-3 2V5a2 2 0 0 1 2-2Zm3 6h6M10 13h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @case('pembayaran')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 7h16v10H4V7Zm0 3h16M7 15h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @case('laporan')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M6 3h9l3 3v15H6V3Zm8 0v4h4M9 17h6M9 13h6M9 9h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @case('notifikasi')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break
                                @default
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                            @endswitch
                        </span>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                @empty
                    <div class="rounded-xl border border-white/10 bg-white/10 p-4 text-sm text-blue-50">
                        Menu tersedia setelah login.
                    </div>
                @endforelse
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="rounded-xl bg-white/10 p-3">
                    <p class="truncate text-sm font-semibold">{{ $user?->name ?? 'Pengguna' }}</p>
                    <p class="mt-1 text-xs text-blue-100">{{ $displayRole }}</p>
                </div>
            </div>
        </aside>

        <div class="lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-card/95 backdrop-blur">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 bg-white p-2 text-slate-600 shadow-sm hover:bg-slate-50 lg:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka sidebar"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>

                        <div class="min-w-0">
                            <h1 class="truncate text-lg font-bold text-secondary sm:text-xl">
                                {{ $header ?? $__env->yieldContent('header') ?: $pageTitle }}
                            </h1>
                            @isset($breadcrumbs)
                                <div class="mt-1 text-sm text-slate-500">
                                    {{ $breadcrumbs }}
                                </div>
                            @else
                                @hasSection('breadcrumbs')
                                    <div class="mt-1 text-sm text-slate-500">
                                        @yield('breadcrumbs')
                                    </div>
                                @endif
                            @endisset
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                        <a
                            href="{{ $notificationUrl }}"
                            class="relative rounded-xl border border-slate-200 bg-white p-2.5 text-slate-600 shadow-sm hover:bg-slate-50 hover:text-primary"
                            aria-label="Buka notifikasi"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M18 9a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span
                                x-cloak
                                x-show="notificationCount > 0"
                                x-text="notificationCount > 99 ? '99+' : notificationCount"
                                class="absolute -right-1 -top-1 min-w-5 rounded-full bg-danger px-1.5 py-0.5 text-center text-[10px] font-bold leading-4 text-white"
                            ></span>
                        </a>

                        <div class="relative" @keydown.escape.window="profileOpen = false">
                            <button
                                type="button"
                                class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-left shadow-sm hover:bg-slate-50"
                                @click="profileOpen = !profileOpen"
                                aria-haspopup="true"
                                :aria-expanded="profileOpen.toString()"
                            >
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">
                                    {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                                </span>
                                <span class="hidden min-w-0 sm:block">
                                    <span class="block max-w-40 truncate text-sm font-semibold text-secondary">{{ $user?->name ?? 'Pengguna' }}</span>
                                    <span class="mt-0.5 inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-primary">{{ $displayRole }}</span>
                                </span>
                                <svg class="hidden h-4 w-4 text-slate-400 sm:block" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <div
                                x-cloak
                                x-show="profileOpen"
                                x-transition.origin.top.right
                                @click.outside="profileOpen = false"
                                class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg"
                            >
                                <div class="border-b border-slate-100 px-4 py-3">
                                    <p class="truncate text-sm font-semibold text-secondary">{{ $user?->name ?? 'Pengguna' }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $user?->email ?? $user?->username ?? '-' }}</p>
                                </div>
                                <a href="{{ $profileUrl }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                    Profil
                                </a>
                                <form method="POST" action="{{ $logoutUrl }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2.5 text-left text-sm font-medium text-danger hover:bg-red-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="min-h-[calc(100vh-8rem)] px-4 py-6 sm:px-6 lg:px-8">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>

            <footer class="border-t border-slate-200 bg-card px-4 py-4 text-center text-xs text-slate-500 sm:px-6 lg:px-8">
                &copy; {{ date('Y') }} Sistem Informasi Pembayaran Uang Sekolah.
            </footer>
        </div>
    </div>
</body>
</html>
