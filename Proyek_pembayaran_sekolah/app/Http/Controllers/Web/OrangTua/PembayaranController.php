<?php

namespace App\Http\Controllers\Web\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\ResolvesParentProfile;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    use ResolvesParentProfile;

    public function index(Request $request)
    {
        $orangTua = $this->resolveParentProfile($request->user(), ['siswas.kelas']);

        $siswaIds = $orangTua->siswas->pluck('id');

        $pembayarans = Pembayaran::with(['tagihanSiswa.tagihan', 'tagihanSiswa.siswa.kelas'])
            ->whereHas('tagihanSiswa', fn ($query) => $query->whereIn('siswa_id', $siswaIds))
            ->when($request->filled('siswa_id'), function ($query) use ($request, $siswaIds) {
                $siswaId = $request->input('siswa_id');

                if ($siswaIds->contains((int) $siswaId)) {
                    $query->whereHas('tagihanSiswa', fn ($query) => $query->where('siswa_id', $siswaId));
                }
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('order_id', 'like', "%{$search}%")
                        ->orWhere('payment_type', 'like', "%{$search}%")
                        ->orWhere('transaction_status', 'like', "%{$search}%")
                        ->orWhereHas('tagihanSiswa.siswa', fn ($siswa) => $siswa->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                        ->orWhereHas('tagihanSiswa.tagihan', fn ($tagihan) => $tagihan->where('judul', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('transaction_status', $request->input('status')))
            ->latest()
            ->paginate(10)
            ->appends($request->only(['search', 'status', 'siswa_id']));

        $statusOptions = Pembayaran::query()
            ->whereHas('tagihanSiswa', fn ($query) => $query->whereIn('siswa_id', $siswaIds))
            ->select('transaction_status')
            ->whereNotNull('transaction_status')
            ->distinct()
            ->orderBy('transaction_status')
            ->pluck('transaction_status');

        $totalPembayaran = Pembayaran::query()
            ->whereHas('tagihanSiswa', fn ($query) => $query->whereIn('siswa_id', $siswaIds))
            ->sum('gross_amount');

        $jumlahTransaksi = Pembayaran::query()
            ->whereHas('tagihanSiswa', fn ($query) => $query->whereIn('siswa_id', $siswaIds))
            ->count();

        return view('orang-tua.pembayaran.index', compact(
            'orangTua',
            'pembayarans',
            'statusOptions',
            'totalPembayaran',
            'jumlahTransaksi'
        ));
    }
}
