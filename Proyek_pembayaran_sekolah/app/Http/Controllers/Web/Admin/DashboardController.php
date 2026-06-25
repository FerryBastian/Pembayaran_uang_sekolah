<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TagihanSiswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $successfulStatuses = ['lunas'];

        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalTagihanBulanIni = Tagihan::where('bulan', $now->month)
            ->where('tahun', $now->year)
            ->count();
        $totalTerkumpulBulanIni = Pembayaran::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->where(function ($query) use ($successfulStatuses) {
                $query->whereIn('transaction_status', $successfulStatuses)
                    ->orWhereHas('tagihanSiswa', fn ($tagihanSiswa) => $tagihanSiswa->where('status', 'lunas'));
            })
            ->sum('gross_amount');

        $months = collect(range(5, 0))->map(fn ($index) => $now->copy()->subMonths($index));
        $paymentRows = Pembayaran::selectRaw('YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(gross_amount) as total')
            ->where('created_at', '>=', $months->first()->copy()->startOfMonth())
            ->where(function ($query) use ($successfulStatuses) {
                $query->whereIn('transaction_status', $successfulStatuses)
                    ->orWhereHas('tagihanSiswa', fn ($tagihanSiswa) => $tagihanSiswa->where('status', 'lunas'));
            })
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy(fn ($row) => "{$row->tahun}-{$row->bulan}");

        $paymentChart = [
            'labels' => $months->map(fn (Carbon $date) => $date->translatedFormat('M Y'))->values(),
            'values' => $months->map(fn (Carbon $date) => (float) ($paymentRows->get("{$date->year}-{$date->month}")->total ?? 0))->values(),
        ];

        $tagihanTerbaru = Tagihan::withCount([
                'tagihanSiswas as total_siswa',
                'tagihanSiswas as lunas_count' => fn ($query) => $query->where('status', 'lunas'),
                'tagihanSiswas as pending_count' => fn ($query) => $query->where('status', 'pending'),
                'tagihanSiswas as belum_bayar_count' => fn ($query) => $query->where('status', 'belum_bayar'),
            ])
            ->latest()
            ->limit(5)
            ->get();

        $pembayaranTerbaru = Pembayaran::with([
                'tagihanSiswa.tagihan',
                'tagihanSiswa.siswa.kelas',
            ])
            ->latest()
            ->limit(5)
            ->get();

        $statusPembayaran = [
            'lunas' => TagihanSiswa::where('status', 'lunas')->count(),
            'pending' => TagihanSiswa::where('status', 'pending')->count(),
            'belum_bayar' => TagihanSiswa::where('status', 'belum_bayar')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalGuru',
            'totalTagihanBulanIni',
            'totalTerkumpulBulanIni',
            'paymentChart',
            'tagihanTerbaru',
            'pembayaranTerbaru',
            'statusPembayaran'
        ));
    }
}
