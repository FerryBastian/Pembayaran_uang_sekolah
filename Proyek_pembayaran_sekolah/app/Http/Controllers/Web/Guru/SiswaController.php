<?php

namespace App\Http\Controllers\Web\Guru;

use App\Http\Controllers\Controller;

class SiswaController extends Controller
{
    public function index() { abort(501, 'Data siswa guru akan dibuat pada FASE 3 nomor 16.'); }
    public function show(string $siswa) { abort(501, 'Detail siswa guru akan dibuat pada FASE 3 nomor 16.'); }
}
