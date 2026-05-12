<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kelas\StoreKelasRequest;
use App\Http\Requests\Kelas\UpdateKelasRequest;
use App\Models\Kelas;
use App\Repositories\KelasRepository;
use Exception;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function __construct(private readonly KelasRepository $kelasRepository)
    {
    }

    public function index(Request $request)
    {
        $kelas = $this->kelasRepository->getAll([
            'search' => $request->string('search')->toString(),
            'per_page' => 10,
        ]);

        $kelas->appends($request->only('search'));

        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return redirect()->route('admin.kelas.index');
    }

    public function store(StoreKelasRequest $request)
    {
        $this->kelasRepository->create($request->validated());

        return redirect()
            ->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        return redirect()->route('admin.kelas.index');
    }

    public function update(UpdateKelasRequest $request, Kelas $kelas)
    {
        $this->kelasRepository->update($kelas->id, $request->validated());

        return redirect()
            ->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        try {
            $this->kelasRepository->delete($kelas->id);

            return redirect()
                ->route('admin.kelas.index')
                ->with('success', 'Kelas berhasil dihapus.');
        } catch (Exception $exception) {
            return redirect()
                ->route('admin.kelas.index')
                ->with('error', $exception->getMessage());
        }
    }
}
