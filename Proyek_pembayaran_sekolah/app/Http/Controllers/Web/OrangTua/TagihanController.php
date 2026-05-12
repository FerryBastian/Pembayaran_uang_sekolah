<?php

namespace App\Http\Controllers\Web\OrangTua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index() { abort(501, 'Tagihan anak akan dibuat pada FASE 4 nomor 19.'); }
    public function bayar(string $tagihanSiswa) { abort(501, 'Halaman bayar akan dibuat pada FASE 4 nomor 19.'); }
    public function snapToken(Request $request, string $tagihanSiswa) { abort(501, 'Midtrans Snap akan dibuat pada FASE 4 nomor 19.'); }
}
