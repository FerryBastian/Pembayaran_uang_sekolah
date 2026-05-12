<?php

namespace App\Http\Controllers\Web\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TagihanSiswa;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();

        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $tagihanBulanIni = Tagihan::where('bulan', $now->month)->where('tahun', $now->year)->count();

        $statusPembayaran = [
            'lunas' => TagihanSiswa::where('status', 'lunas')->count(),
            'pending' => TagihanSiswa::where('status', 'pending')->count(),
            'belum_bayar' => TagihanSiswa::where('status', 'belum_bayar')->count(),
        ];

        $kelasRingkas = Kelas::withCount('siswas')->orderBy('nama_kelas')->limit(8)->get();
        $tagihanTerbaru = Tagihan::withCount([
                'tagihanSiswas as total_siswa',
                'tagihanSiswas as lunas_count' => fn ($query) => $query->where('status', 'lunas'),
            ])
            ->latest()
            ->limit(5)
            ->get();

        return view('guru.dashboard', compact(
            'totalSiswa',
            'totalKelas',
            'tagihanBulanIni',
            'statusPembayaran',
            'kelasRingkas',
            'tagihanTerbaru'
        ));
    }
}
