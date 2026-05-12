<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $pembayarans = Pembayaran::with(['tagihanSiswa.tagihan', 'tagihanSiswa.siswa.kelas'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                        ->orWhere('payment_type', 'like', "%{$search}%")
                        ->orWhere('transaction_status', 'like', "%{$search}%")
                        ->orWhereHas('tagihanSiswa.siswa', fn ($siswa) => $siswa->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                        ->orWhereHas('tagihanSiswa.tagihan', fn ($tagihan) => $tagihan->where('judul', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('transaction_status', $request->status))
            ->latest()
            ->paginate(15)
            ->appends($request->only(['search', 'status']));

        $statusOptions = Pembayaran::query()
            ->select('transaction_status')
            ->whereNotNull('transaction_status')
            ->distinct()
            ->orderBy('transaction_status')
            ->pluck('transaction_status');

        return view('admin.pembayaran.index', compact('pembayarans', 'statusOptions'));
    }
}
