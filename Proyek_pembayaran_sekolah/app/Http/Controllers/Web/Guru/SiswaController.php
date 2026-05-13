<?php

namespace App\Http\Controllers\Web\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $siswas = Siswa::with(['user', 'kelas', 'orangTua'])
            ->when($request->filled('kelas_id'), fn ($query) => $query->where('kelas_id', $request->input('kelas_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhereHas('kelas', fn ($query) => $query->where('nama_kelas', 'like', "%{$search}%"))
                        ->orWhereHas('orangTua', fn ($query) => $query->where('nama', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->appends($request->only(['search', 'kelas_id']));

        $kelasOptions = Kelas::orderBy('nama_kelas')->get();

        return view('guru.siswa.index', compact('siswas', 'kelasOptions'));
    }

    public function show(Siswa $siswa)
    {
        $siswa->load([
            'user',
            'kelas',
            'orangTua.user',
            'tagihanSiswas.tagihan',
            'tagihanSiswas.pembayaran',
        ]);

        return view('guru.siswa.show', compact('siswa'));
    }
}
