<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $siswa = $request->user()
            ->siswa()
            ->with('kelas')
            ->firstOrFail();

        $pembayarans = Pembayaran::with(['tagihanSiswa.tagihan'])
            ->whereHas('tagihanSiswa', fn ($query) => $query->where('siswa_id', $siswa->id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('order_id', 'like', "%{$search}%")
                        ->orWhere('payment_type', 'like', "%{$search}%")
                        ->orWhere('transaction_status', 'like', "%{$search}%")
                        ->orWhereHas('tagihanSiswa.tagihan', fn ($tagihan) => $tagihan->where('judul', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('transaction_status', $request->input('status')))
            ->latest()
            ->paginate(10)
            ->appends($request->only(['search', 'status']));

        $statusOptions = Pembayaran::query()
            ->whereHas('tagihanSiswa', fn ($query) => $query->where('siswa_id', $siswa->id))
            ->select('transaction_status')
            ->whereNotNull('transaction_status')
            ->distinct()
            ->orderBy('transaction_status')
            ->pluck('transaction_status');

        $totalPembayaran = Pembayaran::query()
            ->whereHas('tagihanSiswa', fn ($query) => $query->where('siswa_id', $siswa->id))
            ->sum('gross_amount');

        $jumlahTransaksi = Pembayaran::query()
            ->whereHas('tagihanSiswa', fn ($query) => $query->where('siswa_id', $siswa->id))
            ->count();

        return view('siswa.pembayaran.index', compact(
            'siswa',
            'pembayarans',
            'statusOptions',
            'totalPembayaran',
            'jumlahTransaksi'
        ));
    }
}
