<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanSiswa;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa = auth()->user()
            ->siswa()
            ->with(['kelas', 'orangTua'])
            ->firstOrFail();

        $now = now();

        $tagihanBulanIni = TagihanSiswa::with(['tagihan', 'pembayaran'])
            ->where('siswa_id', $siswa->id)
            ->whereHas('tagihan', fn ($query) => $query->where('bulan', $now->month)->where('tahun', $now->year))
            ->latest()
            ->get();

        $tagihanAktif = TagihanSiswa::with(['tagihan', 'pembayaran'])
            ->where('siswa_id', $siswa->id)
            ->whereIn('status', ['belum_bayar', 'pending'])
            ->orderByRaw('CASE WHEN jatuh_tempo IS NULL THEN 1 ELSE 0 END')
            ->orderBy('jatuh_tempo')
            ->limit(5)
            ->get();

        $statusPembayaran = [
            'lunas' => TagihanSiswa::where('siswa_id', $siswa->id)->where('status', 'lunas')->count(),
            'pending' => TagihanSiswa::where('siswa_id', $siswa->id)->where('status', 'pending')->count(),
            'belum_bayar' => TagihanSiswa::where('siswa_id', $siswa->id)->where('status', 'belum_bayar')->count(),
        ];

        $totalBelumBayar = TagihanSiswa::with('tagihan')
            ->where('siswa_id', $siswa->id)
            ->where('status', 'belum_bayar')
            ->get()
            ->sum(fn ($row) => (float) ($row->tagihan?->nominal ?? 0));

        $pembayaranTerbaru = Pembayaran::with(['tagihanSiswa.tagihan'])
            ->whereHas('tagihanSiswa', fn ($query) => $query->where('siswa_id', $siswa->id))
            ->latest()
            ->limit(5)
            ->get();

        return view('siswa.dashboard', compact(
            'siswa',
            'tagihanBulanIni',
            'tagihanAktif',
            'statusPembayaran',
            'totalBelumBayar',
            'pembayaranTerbaru'
        ));
    }
}
