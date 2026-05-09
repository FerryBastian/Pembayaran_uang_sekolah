<?php

namespace App\Http\Controllers;

use App\Repositories\GuruRepository;
use App\Http\Requests\Guru\StoreGuruRequest;
use App\Http\Requests\Guru\UpdateGuruRequest;
use App\Http\Resources\GuruResource;

class GuruController extends Controller
{
    public function __construct(
        protected GuruRepository $guruRepository
    ) {}

    public function index()
    {
        $filters = request()->only(['search', 'per_page']);
        $gurus   = $this->guruRepository->getAll($filters);

        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil diambil.',
            'data'    => GuruResource::collection($gurus),
            'meta'    => [
                'current_page' => $gurus->currentPage(),
                'last_page'    => $gurus->lastPage(),
                'per_page'     => $gurus->perPage(),
                'total'        => $gurus->total(),
            ],
        ]);
    }

    public function store(StoreGuruRequest $request)
    {
        $guru = $this->guruRepository->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Guru berhasil ditambahkan.',
            'data'    => new GuruResource($guru),
        ], 201);
    }

    public function show(int $id)
    {
        $guru = $this->guruRepository->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail guru berhasil diambil.',
            'data'    => new GuruResource($guru),
        ]);
    }

    public function update(UpdateGuruRequest $request, int $id)
    {
        $guru = $this->guruRepository->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil diupdate.',
            'data'    => new GuruResource($guru),
        ]);
    }

    public function destroy(int $id)
    {
        $this->guruRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Guru berhasil dihapus.',
        ]);
    }
}