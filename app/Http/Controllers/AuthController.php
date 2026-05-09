<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari user by username ATAU email
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        // Hapus token lama (single session per device)
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // Load relasi profil sesuai role
        $user->load(match($user->role) {
            'siswa'     => ['siswa.kelas'],
            'orang_tua' => ['orangTua.siswas'],
            'guru'      => ['guru'],
            default     => [],
        });

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => $user,
            ],
        ]);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * GET /api/me
     */
    public function me(Request $request)
    {
        $user = $request->user();

        $user->load(match($user->role) {
            'siswa'     => ['siswa.kelas', 'siswa.orangTua'],
            'orang_tua' => ['orangTua.siswas.kelas'],
            'guru'      => ['guru'],
            default     => [],
        });

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diambil.',
            'data'    => $user,
        ]);
    }
}