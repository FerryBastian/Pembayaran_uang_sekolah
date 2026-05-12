<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\StoreGuruRequest;
use App\Http\Requests\Guru\UpdateGuruRequest;
use App\Models\Guru;
use App\Repositories\GuruRepository;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function __construct(private readonly GuruRepository $guruRepository)
    {
    }

    public function index(Request $request)
    {
        $gurus = $this->guruRepository->getAll([
            'search' => $request->string('search')->toString(),
            'per_page' => 10,
        ]);

        $gurus->appends($request->only('search'));

        return view('admin.guru.index', compact('gurus'));
    }

    public function create()
    {
        return redirect()->route('admin.guru.index');
    }

    public function store(StoreGuruRequest $request)
    {
        $this->guruRepository->create($request->validated());

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(Guru $guru)
    {
        return redirect()->route('admin.guru.index');
    }

    public function update(UpdateGuruRequest $request, Guru $guru)
    {
        $this->guruRepository->update($guru->id, $request->validated());

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Guru $guru)
    {
        $this->guruRepository->delete($guru->id);

        return redirect()
            ->route('admin.guru.index')
            ->with('success', 'Guru berhasil dihapus.');
    }
}
