<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function edit()
    {
        abort(501, 'Halaman profil akan dibuat pada FASE 6 nomor 25.');
    }

    public function update(Request $request)
    {
        abort(501, 'Update profil akan dibuat pada FASE 6 nomor 25.');
    }

    public function updatePassword(Request $request)
    {
        abort(501, 'Ganti password akan dibuat pada FASE 6 nomor 25.');
    }
}
