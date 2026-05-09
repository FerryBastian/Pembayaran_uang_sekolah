<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\TagihanController;

// ─── Public Routes ───────────────────────────────────────────────
Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Login endpoint requires POST method. Please use POST to /api/login with username and password.',
        'documentation' => 'Use POST with body: {username: string, password: string}'
    ], 405);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

// ─── Protected Routes ────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── MULTIPLE ROLES (Admin, Orang Tua, Siswa) ──────────────────
    // Akses index tagihan diletakkan di luar middleware role spesifik 
    // karena controller sudah mengatur logika data berdasarkan user login.
    Route::get('/tagihan', [TagihanController::class, 'index']);

    // ── ADMIN ONLY ──────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('kelas',     KelasController::class);
        Route::apiResource('orang-tua', OrangTuaController::class);
        
        // Tagihan CRUD (kecuali index yang sudah didefinisikan di atas) & Assign Manual
        Route::apiResource('tagihan', TagihanController::class)->except(['index']);
        Route::post('/tagihan/{id}/assign', [TagihanController::class, 'assignSiswa']);
    });

    // ── ADMIN + GURU ────────────────────────────────────────────
    Route::middleware('role:admin,guru')->group(function () {
        Route::apiResource('siswa', SiswaController::class);
        Route::apiResource('guru', GuruController::class);
    });

    // ── ORANG TUA ───────────────────────────────────────────────
    Route::middleware('role:orang_tua')->group(function () {
        // Akan diisi pada Step 8 (Pembayaran)
    });

    // ── SISWA ───────────────────────────────────────────────────
    Route::middleware('role:siswa')->group(function () {
        // Akan diisi pada Step 8 (Pembayaran)
    });
});