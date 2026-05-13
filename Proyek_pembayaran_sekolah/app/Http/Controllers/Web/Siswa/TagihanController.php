<?php

namespace App\Http\Controllers\Web\Siswa;

use App\Http\Controllers\Controller;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $siswa = $request->user()
            ->siswa()
            ->with('kelas')
            ->firstOrFail();

        $tagihanSiswas = TagihanSiswa::with(['tagihan', 'pembayaran'])
            ->where('siswa_id', $siswa->id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->whereHas('tagihan', function ($query) use ($search) {
                    $query->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhere('tahun', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->appends($request->only(['search', 'status']));

        $statusCounts = [
            'belum_bayar' => TagihanSiswa::where('siswa_id', $siswa->id)->where('status', 'belum_bayar')->count(),
            'pending' => TagihanSiswa::where('siswa_id', $siswa->id)->where('status', 'pending')->count(),
            'lunas' => TagihanSiswa::where('siswa_id', $siswa->id)->where('status', 'lunas')->count(),
        ];

        $totalBelumBayar = TagihanSiswa::with('tagihan')
            ->where('siswa_id', $siswa->id)
            ->where('status', 'belum_bayar')
            ->get()
            ->sum(fn ($row) => (float) ($row->tagihan?->nominal ?? 0));

        return view('siswa.tagihan.index', compact('siswa', 'tagihanSiswas', 'statusCounts', 'totalBelumBayar'));
    }
}
