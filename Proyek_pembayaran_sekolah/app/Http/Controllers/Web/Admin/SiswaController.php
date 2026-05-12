<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\StoreSiswaRequest;
use App\Http\Requests\Siswa\UpdateSiswaRequest;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Repositories\SiswaRepository;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function __construct(private readonly SiswaRepository $siswaRepository)
    {
    }

    public function index(Request $request)
    {
        $siswas = $this->siswaRepository->getAll([
            'search' => $request->string('search')->toString(),
            'kelas_id' => $request->input('kelas_id'),
            'per_page' => 10,
        ]);

        $siswas->appends($request->only(['search', 'kelas_id']));

        $kelasOptions = Kelas::orderBy('nama_kelas')->get();
        $orangTuaOptions = OrangTua::orderBy('nama')->get();

        return view('admin.siswa.index', compact('siswas', 'kelasOptions', 'orangTuaOptions'));
    }

    public function create()
    {
        return redirect()->route('admin.siswa.index');
    }

    public function store(StoreSiswaRequest $request)
    {
        $this->siswaRepository->create($request->validated());

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        return redirect()->route('admin.siswa.index');
    }

    public function update(UpdateSiswaRequest $request, Siswa $siswa)
    {
        $this->siswaRepository->update($siswa->id, $request->validated());

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $this->siswaRepository->delete($siswa->id);

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
