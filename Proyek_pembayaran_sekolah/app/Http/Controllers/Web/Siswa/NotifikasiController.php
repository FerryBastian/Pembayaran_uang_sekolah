<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index() { abort(501, 'Notifikasi akan dibuat pada FASE 6 nomor 24.'); }
    public function markAsRead(string $notifikasi) { abort(501, 'Notifikasi akan dibuat pada FASE 6 nomor 24.'); }
    public function markAllAsRead(Request $request) { abort(501, 'Notifikasi akan dibuat pada FASE 6 nomor 24.'); }
}
