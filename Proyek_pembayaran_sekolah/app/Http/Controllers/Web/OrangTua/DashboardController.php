<?php

namespace App\Http\Controllers\Web\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\ResolvesParentProfile;
use App\Models\TagihanSiswa;

class DashboardController extends Controller
{
    use ResolvesParentProfile;

    public function index()
    {
        $orangTua = $this->resolveParentProfile(auth()->user(), ['siswas.kelas']);

        $siswaIds = $orangTua->siswas->pluck('id');

        $tagihanSiswas = TagihanSiswa::with(['tagihan', 'siswa.kelas', 'pembayaran'])
            ->whereIn('siswa_id', $siswaIds)
            ->latest()
            ->get();

        $tagihanPerAnak = $tagihanSiswas->groupBy('siswa_id');
        $tagihanAktif = $tagihanSiswas
            ->whereIn('status', ['belum_bayar', 'pending'])
            ->sortBy('jatuh_tempo');

        $totalBelumBayar = $tagihanSiswas
            ->where('status', 'belum_bayar')
            ->sum(fn ($row) => (float) ($row->tagihan?->nominal ?? 0));

        $totalPending = $tagihanSiswas
            ->where('status', 'pending')
            ->sum(fn ($row) => (float) ($row->tagihan?->nominal ?? 0));

        $jumlahBelumBayar = $tagihanSiswas->where('status', 'belum_bayar')->count();
        $jumlahPending = $tagihanSiswas->where('status', 'pending')->count();
        $jumlahLunas = $tagihanSiswas->where('status', 'lunas')->count();

        return view('orang-tua.dashboard', compact(
            'orangTua',
            'tagihanPerAnak',
            'tagihanAktif',
            'totalBelumBayar',
            'totalPending',
            'jumlahBelumBayar',
            'jumlahPending',
            'jumlahLunas'
        ));
    }
}
