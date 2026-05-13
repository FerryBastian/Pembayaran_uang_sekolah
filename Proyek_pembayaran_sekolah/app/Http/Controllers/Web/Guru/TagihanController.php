<?php

namespace App\Http\Controllers\Web\Guru;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $tagihans = Tagihan::withCount([
                'tagihanSiswas as total_siswa',
                'tagihanSiswas as lunas_count' => fn ($query) => $query->where('status', 'lunas'),
                'tagihanSiswas as pending_count' => fn ($query) => $query->where('status', 'pending'),
                'tagihanSiswas as belum_bayar_count' => fn ($query) => $query->where('status', 'belum_bayar'),
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhere('tahun', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('bulan'), fn ($query) => $query->where('bulan', $request->integer('bulan')))
            ->when($request->filled('tahun'), fn ($query) => $query->where('tahun', $request->integer('tahun')))
            ->latest()
            ->paginate(10)
            ->appends($request->only(['search', 'bulan', 'tahun']));

        return view('guru.tagihan.index', [
            'tagihans' => $tagihans,
            'months' => $this->months(),
            'years' => range((int) now()->year - 2, (int) now()->year + 2),
        ]);
    }

    public function show(Request $request, Tagihan $tagihan)
    {
        $tagihan->loadCount([
            'tagihanSiswas as total_siswa',
            'tagihanSiswas as lunas_count' => fn ($query) => $query->where('status', 'lunas'),
            'tagihanSiswas as pending_count' => fn ($query) => $query->where('status', 'pending'),
            'tagihanSiswas as belum_bayar_count' => fn ($query) => $query->where('status', 'belum_bayar'),
        ]);

        $tagihanSiswas = $tagihan->tagihanSiswas()
            ->with(['siswa.kelas', 'pembayaran'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->appends($request->only('status'));

        return view('guru.tagihan.show', [
            'tagihan' => $tagihan,
            'tagihanSiswas' => $tagihanSiswas,
            'months' => $this->months(),
        ]);
    }

    private function months(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }
}
