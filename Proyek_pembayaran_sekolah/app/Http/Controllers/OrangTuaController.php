<?php

namespace App\Http\Controllers;

use App\Repositories\OrangTuaRepository;
use App\Http\Requests\OrangTua\StoreOrangTuaRequest;
use App\Http\Requests\OrangTua\UpdateOrangTuaRequest;
use App\Http\Resources\OrangTuaResource;

class OrangTuaController extends Controller
{
    public function __construct(
        protected OrangTuaRepository $orangTuaRepository
    ) {}

    public function index()
    {
        $filters  = request()->only(['search', 'per_page']);
        $orangTua = $this->orangTuaRepository->getAll($filters);

        return response()->json([
            'success' => true, 
            'message' => 'Data orang tua berhasil diambil.',
            'data'    => OrangTuaResource::collection($orangTua),
            'meta'    => [
                'current_page' => $orangTua->currentPage(),
                'last_page'    => $orangTua->lastPage(),
                'per_page'     => $orangTua->perPage(),
                'total'        => $orangTua->total(),
            ],
        ]);
    }

    public function store(StoreOrangTuaRequest $request)
    {
        $orangTua = $this->orangTuaRepository->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Orang tua berhasil ditambahkan.',
            'data'    => new OrangTuaResource($orangTua),
        ], 201);
    }

    public function show(int $id)
    {
        $orangTua = $this->orangTuaRepository->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail orang tua berhasil diambil.',
            'data'    => new OrangTuaResource($orangTua),
        ]);
    }

    public function update(UpdateOrangTuaRequest $request, int $id)
    {
        $orangTua = $this->orangTuaRepository->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data orang tua berhasil diupdate.',
            'data'    => new OrangTuaResource($orangTua),
        ]);
    }

    public function destroy(int $id)
    {
        $this->orangTuaRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Orang tua berhasil dihapus.',
        ]);
    }
}