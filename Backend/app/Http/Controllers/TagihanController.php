<?php

namespace App\Http\Controllers;

use App\Repositories\TagihanRepository;
use App\Http\Requests\Tagihan\StoreTagihanRequest;
use App\Http\Requests\Tagihan\UpdateTagihanRequest;
use App\Http\Resources\TagihanResource;
use App\Http\Resources\TagihanSiswaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagihanController extends Controller
{
    protected $tagihanRepository;

    public function __construct(TagihanRepository $tagihanRepository)
    {
        $this->tagihanRepository = $tagihanRepository;
    }

    private function sendResponse($data, $message = 'Success', $meta = null)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];

        if ($meta) {
            $response['meta'] = $meta;
        }

        return response()->json($response);
    }

    private function sendError($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => null
        ], $code);
    }

    private function getPaginationMeta($paginator)
    {
        return [
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ]
        ];
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 10);

            if ($user->role === 'admin') {
                $filters = $request->only(['search', 'bulan', 'tahun']);
                $tagihans = $this->tagihanRepository->getAll($filters, $perPage);
                
                return $this->sendResponse(
                    TagihanResource::collection($tagihans),
                    'Berhasil mengambil data tagihan',
                    $this->getPaginationMeta($tagihans)
                );
            } 
            elseif ($user->role === 'siswa') {
                $siswaId = $user->siswa->id; // Asumsi relasi user->siswa sudah dibuat di model User
                $tagihans = $this->tagihanRepository->getTagihanBySiswa($siswaId, $perPage);
                
                return $this->sendResponse(
                    TagihanSiswaResource::collection($tagihans),
                    'Berhasil mengambil data tagihan siswa',
                    $this->getPaginationMeta($tagihans)
                );
            } 
            elseif ($user->role === 'orang_tua') {
                $orangTuaId = $user->orangTua->id; // Asumsi relasi user->orangTua sudah dibuat di model User
                $tagihans = $this->tagihanRepository->getTagihanByOrangTua($orangTuaId, $perPage);
                
                return $this->sendResponse(
                    TagihanSiswaResource::collection($tagihans),
                    'Berhasil mengambil data tagihan anak',
                    $this->getPaginationMeta($tagihans)
                );
            }

            return $this->sendError('Unauthorized role', 403);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function store(StoreTagihanRequest $request)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                return $this->sendError('Akses ditolak', 403);
            }

            $tagihan = $this->tagihanRepository->create($request->validated(), Auth::id());
            
            return $this->sendResponse(
                new TagihanResource($tagihan),
                'Berhasil membuat tagihan dan assign ke semua siswa aktif',
                null
            );
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                $tagihan = $this->tagihanRepository->findById($id);
                // Load counts
                $tagihan->loadCount([
                    'tagihanSiswas as total_siswa',
                    'tagihanSiswas as sudah_bayar' => function ($q) { $q->where('status', 'lunas'); },
                    'tagihanSiswas as belum_bayar' => function ($q) { $q->where('status', 'belum_bayar'); },
                    'tagihanSiswas as pending' => function ($q) { $q->where('status', 'pending'); }
                ]);
                
                return $this->sendResponse(new TagihanResource($tagihan), 'Berhasil mengambil detail tagihan');
            }

            return $this->sendError('Detail khusus admin, siswa & orang tua gunakan index', 403);
        } catch (\Exception $e) {
            return $this->sendError('Data tidak ditemukan atau terjadi kesalahan: ' . $e->getMessage(), 404);
        }
    }

    public function update(UpdateTagihanRequest $request, $id)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                return $this->sendError('Akses ditolak', 403);
            }

            $tagihan = $this->tagihanRepository->update($id, $request->validated());
            
            return $this->sendResponse(new TagihanResource($tagihan), 'Berhasil mengupdate tagihan');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                return $this->sendError('Akses ditolak', 403);
            }

            $this->tagihanRepository->delete($id);
            
            return $this->sendResponse(null, 'Berhasil menghapus tagihan');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }

    public function assignSiswa(Request $request, $id)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                return $this->sendError('Akses ditolak', 403);
            }

            $request->validate([
                'siswa_id' => 'required|exists:siswas,id'
            ]);

            $tagihanSiswa = $this->tagihanRepository->assignManual($id, $request->siswa_id);
            
            return $this->sendResponse(
                new TagihanSiswaResource($tagihanSiswa), 
                'Berhasil assign tagihan secara manual ke siswa'
            );
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }
}