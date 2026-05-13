<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        'orang_tua' => redirect()->route('orang-tua.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        default => redirect()->route('login'),
    };
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Web\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Web\Auth\LoginController::class, 'login'])->name('login.store');
});

Route::post('/logout', [\App\Http\Controllers\Web\Auth\LoginController::class, 'logout'])
    ->middleware('auth.web')
    ->name('logout');

Route::middleware(['auth.web', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Web\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('kelas', \App\Http\Controllers\Web\Admin\KelasController::class)
            ->parameters(['kelas' => 'kelas'])
            ->except(['show']);
        Route::resource('guru', \App\Http\Controllers\Web\Admin\GuruController::class)->except(['show']);
        Route::resource('orang-tua', \App\Http\Controllers\Web\Admin\OrangTuaController::class)->except(['show']);
        Route::resource('siswa', \App\Http\Controllers\Web\Admin\SiswaController::class)->except(['show']);

        Route::resource('tagihan', \App\Http\Controllers\Web\Admin\TagihanController::class);
        Route::get('/tagihan/{tagihan}/assign', [\App\Http\Controllers\Web\Admin\TagihanController::class, 'assign'])->name('tagihan.assign');
        Route::post('/tagihan/{tagihan}/assign', [\App\Http\Controllers\Web\Admin\TagihanController::class, 'storeAssign'])->name('tagihan.assign.store');
        Route::post('/tagihan/{tagihan}/notifikasi/blast', [\App\Http\Controllers\Web\Admin\TagihanController::class, 'blastWhatsapp'])->name('tagihan.notifikasi.blast');
        Route::post('/tagihan-siswa/{tagihanSiswa}/notifikasi', [\App\Http\Controllers\Web\Admin\TagihanController::class, 'sendWhatsapp'])->name('tagihan-siswa.notifikasi.send');

        Route::get('/pembayaran', [\App\Http\Controllers\Web\Admin\PembayaranController::class, 'index'])->name('pembayaran.index');

        Route::get('/laporan', [\App\Http\Controllers\Web\Admin\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf', [\App\Http\Controllers\Web\Admin\LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

        Route::get('/notifikasi', [\App\Http\Controllers\Web\Admin\NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/count', [\App\Http\Controllers\Web\Admin\NotifikasiController::class, 'count'])->name('notifikasi.count');
        Route::patch('/notifikasi/{notifikasi}/read', [\App\Http\Controllers\Web\Admin\NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
        Route::patch('/notifikasi/read-all', [\App\Http\Controllers\Web\Admin\NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.read-all');

        Route::get('/profil', [\App\Http\Controllers\Web\ProfilController::class, 'edit'])->name('profil.edit');
        Route::patch('/profil', [\App\Http\Controllers\Web\ProfilController::class, 'update'])->name('profil.update');
        Route::patch('/profil/password', [\App\Http\Controllers\Web\ProfilController::class, 'updatePassword'])->name('profil.password.update');
    });

Route::middleware(['auth.web', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Web\Guru\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/siswa', [\App\Http\Controllers\Web\Guru\SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/siswa/{siswa}', [\App\Http\Controllers\Web\Guru\SiswaController::class, 'show'])->name('siswa.show');
        Route::get('/tagihan', [\App\Http\Controllers\Web\Guru\TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/tagihan/{tagihan}', [\App\Http\Controllers\Web\Guru\TagihanController::class, 'show'])->name('tagihan.show');

        Route::get('/notifikasi', [\App\Http\Controllers\Web\Guru\NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/count', [\App\Http\Controllers\Web\Guru\NotifikasiController::class, 'count'])->name('notifikasi.count');
        Route::patch('/notifikasi/{notifikasi}/read', [\App\Http\Controllers\Web\Guru\NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
        Route::patch('/notifikasi/read-all', [\App\Http\Controllers\Web\Guru\NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.read-all');

        Route::get('/profil', [\App\Http\Controllers\Web\ProfilController::class, 'edit'])->name('profil.edit');
        Route::patch('/profil', [\App\Http\Controllers\Web\ProfilController::class, 'update'])->name('profil.update');
        Route::patch('/profil/password', [\App\Http\Controllers\Web\ProfilController::class, 'updatePassword'])->name('profil.password.update');
    });

Route::middleware(['auth.web', 'role:orang_tua'])
    ->prefix('orang-tua')
    ->name('orang-tua.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Web\OrangTua\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/tagihan', [\App\Http\Controllers\Web\OrangTua\TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/tagihan/{tagihanSiswa}/bayar', [\App\Http\Controllers\Web\OrangTua\TagihanController::class, 'bayar'])->name('tagihan.bayar');
        Route::post('/tagihan/{tagihanSiswa}/snap-token', [\App\Http\Controllers\Web\OrangTua\TagihanController::class, 'snapToken'])->name('tagihan.snap-token');
        Route::get('/pembayaran', [\App\Http\Controllers\Web\OrangTua\PembayaranController::class, 'index'])->name('pembayaran.index');

        Route::get('/notifikasi', [\App\Http\Controllers\Web\OrangTua\NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/count', [\App\Http\Controllers\Web\OrangTua\NotifikasiController::class, 'count'])->name('notifikasi.count');
        Route::patch('/notifikasi/{notifikasi}/read', [\App\Http\Controllers\Web\OrangTua\NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
        Route::patch('/notifikasi/read-all', [\App\Http\Controllers\Web\OrangTua\NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.read-all');

        Route::get('/profil', [\App\Http\Controllers\Web\ProfilController::class, 'edit'])->name('profil.edit');
        Route::patch('/profil', [\App\Http\Controllers\Web\ProfilController::class, 'update'])->name('profil.update');
        Route::patch('/profil/password', [\App\Http\Controllers\Web\ProfilController::class, 'updatePassword'])->name('profil.password.update');
    });

Route::middleware(['auth.web', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Web\Siswa\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/tagihan', [\App\Http\Controllers\Web\Siswa\TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/pembayaran', [\App\Http\Controllers\Web\Siswa\PembayaranController::class, 'index'])->name('pembayaran.index');

        Route::get('/notifikasi', [\App\Http\Controllers\Web\Siswa\NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/count', [\App\Http\Controllers\Web\Siswa\NotifikasiController::class, 'count'])->name('notifikasi.count');
        Route::patch('/notifikasi/{notifikasi}/read', [\App\Http\Controllers\Web\Siswa\NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
        Route::patch('/notifikasi/read-all', [\App\Http\Controllers\Web\Siswa\NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.read-all');

        Route::get('/profil', [\App\Http\Controllers\Web\ProfilController::class, 'edit'])->name('profil.edit');
        Route::patch('/profil', [\App\Http\Controllers\Web\ProfilController::class, 'update'])->name('profil.update');
        Route::patch('/profil/password', [\App\Http\Controllers\Web\ProfilController::class, 'updatePassword'])->name('profil.password.update');
    });
