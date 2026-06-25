<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TagihanController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Login endpoint requires POST method. Please use POST to /api/login with username and password.',
        'documentation' => 'Use POST with body: {username: string, password: string}',
    ], 405);
});

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/tagihan', [TagihanController::class, 'index']);

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('kelas', KelasController::class);
        Route::apiResource('orang-tua', OrangTuaController::class);
        Route::apiResource('tagihan', TagihanController::class)->except(['index']);
        Route::post('/tagihan/{id}/assign', [TagihanController::class, 'assignSiswa']);
    });

    Route::middleware('role:admin,guru')->group(function () {
        Route::apiResource('siswa', SiswaController::class);
        Route::apiResource('guru', GuruController::class);
    });
});
