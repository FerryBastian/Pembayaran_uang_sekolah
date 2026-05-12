<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrangTua\StoreOrangTuaRequest;
use App\Http\Requests\OrangTua\UpdateOrangTuaRequest;
use App\Models\OrangTua;
use App\Repositories\OrangTuaRepository;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    public function __construct(private readonly OrangTuaRepository $orangTuaRepository)
    {
    }

    public function index(Request $request)
    {
        $orangTuas = $this->orangTuaRepository->getAll([
            'search' => $request->string('search')->toString(),
            'per_page' => 10,
        ]);

        $orangTuas->appends($request->only('search'));

        return view('admin.orang-tua.index', compact('orangTuas'));
    }

    public function create()
    {
        return redirect()->route('admin.orang-tua.index');
    }

    public function store(StoreOrangTuaRequest $request)
    {
        $this->orangTuaRepository->create($request->validated());

        return redirect()
            ->route('admin.orang-tua.index')
            ->with('success', 'Orang tua berhasil ditambahkan.');
    }

    public function edit(OrangTua $orang_tua)
    {
        return redirect()->route('admin.orang-tua.index');
    }

    public function update(UpdateOrangTuaRequest $request, OrangTua $orang_tua)
    {
        $this->orangTuaRepository->update($orang_tua->id, $request->validated());

        return redirect()
            ->route('admin.orang-tua.index')
            ->with('success', 'Orang tua berhasil diperbarui.');
    }

    public function destroy(OrangTua $orang_tua)
    {
        $this->orangTuaRepository->delete($orang_tua->id);

        return redirect()
            ->route('admin.orang-tua.index')
            ->with('success', 'Orang tua berhasil dihapus.');
    }
}
