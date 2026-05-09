<?php

namespace App\Http\Controllers;

use App\Repositories\KelasRepository;
use App\Http\Requests\Kelas\StoreKelasRequest;
use App\Http\Requests\Kelas\UpdateKelasRequest;
use App\Http\Resources\KelasResource;

class KelasController extends Controller
{
    public function __construct(
        protected KelasRepository $kelasRepository
    ) {}

    public function index()
    {
        $filters = request()->only(['search', 'per_page']);
        $kelas   = $this->kelasRepository->getAll($filters);

        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diambil.',
            'data'    => KelasResource::collection($kelas),
            'meta'    => [
                'current_page' => $kelas->currentPage(),
                'last_page'    => $kelas->lastPage(),
                'per_page'     => $kelas->perPage(),
                'total'        => $kelas->total(),
            ],
        ]);
    }

    public function store(StoreKelasRequest $request)
    {
        $kelas = $this->kelasRepository->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan.',
            'data'    => new KelasResource($kelas),
        ], 201);
    }

    public function show(int $id)
    {
        $kelas = $this->kelasRepository->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail kelas berhasil diambil.',
            'data'    => new KelasResource($kelas),
        ]);
    }

    public function update(UpdateKelasRequest $request, int $id)
    {
        $kelas = $this->kelasRepository->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diupdate.',
            'data'    => new KelasResource($kelas),
        ]);
    }

    public function destroy(int $id)
    {
        try {
            $this->kelasRepository->delete($id);
            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}