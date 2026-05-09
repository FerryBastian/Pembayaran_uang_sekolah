<?php

namespace App\Http\Controllers;

use App\Repositories\SiswaRepository;
use App\Http\Requests\Siswa\StoreSiswaRequest;
use App\Http\Requests\Siswa\UpdateSiswaRequest;
use App\Http\Resources\SiswaResource;

class SiswaController extends Controller
{
    public function __construct(
        protected SiswaRepository $siswaRepository
    ) {}

    /**
     * GET /api/siswa
     */
    public function index()
    {
        $filters = request()->only(['search', 'kelas_id', 'per_page']);
        $siswas  = $this->siswaRepository->getAll($filters);

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diambil.',
            'data'    => SiswaResource::collection($siswas),
            'meta'    => [
                'current_page' => $siswas->currentPage(),
                'last_page'    => $siswas->lastPage(),
                'per_page'     => $siswas->perPage(),
                'total'        => $siswas->total(),
            ],
        ]);
    }

    /**
     * POST /api/siswa
     */
    public function store(StoreSiswaRequest $request)
    {
        $siswa = $this->siswaRepository->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil ditambahkan.',
            'data'    => new SiswaResource($siswa),
        ], 201);
    }

    /**
     * GET /api/siswa/{id}
     */
    public function show(int $id)
    {
        $siswa = $this->siswaRepository->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail siswa berhasil diambil.',
            'data'    => new SiswaResource($siswa),
        ]);
    }

    /**
     * PUT /api/siswa/{id}
     */
    public function update(UpdateSiswaRequest $request, int $id)
    {
        $siswa = $this->siswaRepository->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diupdate.',
            'data'    => new SiswaResource($siswa),
        ]);
    }

    /**
     * DELETE /api/siswa/{id}
     */
    public function destroy(int $id)
    {
        $this->siswaRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dihapus.',
        ]);
    }
}