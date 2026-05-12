<?php

namespace App\Repositories;

use App\Models\Tagihan;
use App\Models\TagihanSiswa;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagihanRepository
{
    public function getAll($filters = [], $perPage = 10)
    {
        $query = Tagihan::query()
            ->with('pembuatTagihan')
            ->withCount([
                'tagihanSiswas as total_siswa',
                'tagihanSiswas as sudah_bayar' => function ($query) {
                    $query->where('status', 'lunas');
                },
                'tagihanSiswas as belum_bayar' => function ($query) {
                    $query->where('status', 'belum_bayar');
                },
                'tagihanSiswas as pending' => function ($query) {
                    $query->where('status', 'pending');
                }
            ]);

        if (isset($filters['search'])) {
            $query->where('judul', 'like', '%' . $filters['search'] . '%');
        }
        if (isset($filters['bulan'])) {
            $query->where('bulan', $filters['bulan']);
        }
        if (isset($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById($id)
    {
        return Tagihan::with(['pembuatTagihan', 'tagihanSiswas.siswa.kelas', 'tagihanSiswas.pembayaran'])->findOrFail($id);
    }

    public function create(array $data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['created_by'] = $userId;
            $jatuhTempoInput = $data['jatuh_tempo'] ?? null;
            unset($data['jatuh_tempo']);

            $tagihan = Tagihan::create($data);

            $siswas = Siswa::pluck('id');
            $tagihanSiswas = [];
            $jatuhTempo = $jatuhTempoInput
                ? Carbon::parse($jatuhTempoInput)
                : Carbon::createFromDate($data['tahun'], $data['bulan'], 1)->endOfMonth();

            foreach ($siswas as $siswaId) {
                $tagihanSiswas[] = [
                    'tagihan_id' => $tagihan->id,
                    'siswa_id' => $siswaId,
                    'status' => 'belum_bayar',
                    'jatuh_tempo' => $jatuhTempo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($tagihanSiswas)) {
                TagihanSiswa::insert($tagihanSiswas);
            }

            return $tagihan;
        });
    }

    public function update($id, array $data)
    {
        $tagihan = Tagihan::findOrFail($id);
        $jatuhTempo = $data['jatuh_tempo'] ?? null;
        unset($data['jatuh_tempo']);

        $tagihan->update($data);

        if ($jatuhTempo) {
            TagihanSiswa::where('tagihan_id', $id)
                ->where('status', 'belum_bayar')
                ->update(['jatuh_tempo' => Carbon::parse($jatuhTempo)]);
        }

        return $tagihan;
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $tagihan = Tagihan::findOrFail($id);
            
            // Cek apakah ada yang sudah lunas atau pending
            $hasActivePayment = TagihanSiswa::where('tagihan_id', $id)
                ->whereIn('status', ['lunas', 'pending'])
                ->exists();

            if ($hasActivePayment) {
                throw new \Exception("Tagihan tidak dapat dihapus karena sudah ada siswa yang membayar atau pending.");
            }

            TagihanSiswa::where('tagihan_id', $id)->delete();
            $tagihan->delete();

            return true;
        });
    }

    public function getTagihanBySiswa($siswaId, $perPage = 10)
    {
        return TagihanSiswa::with(['tagihan', 'siswa.kelas'])
            ->where('siswa_id', $siswaId)
            ->latest()
            ->paginate($perPage);
    }

    public function getTagihanByOrangTua($orangTuaId, $perPage = 10)
    {
        $siswaIds = Siswa::where('orang_tua_id', $orangTuaId)->pluck('id');
        
        return TagihanSiswa::with(['tagihan', 'siswa.kelas'])
            ->whereIn('siswa_id', $siswaIds)
            ->latest()
            ->paginate($perPage);
    }

    public function assignManual($tagihanId, $siswaId)
    {
        $tagihan = Tagihan::findOrFail($tagihanId);
        
        $exists = TagihanSiswa::where('tagihan_id', $tagihanId)
            ->where('siswa_id', $siswaId)
            ->exists();

        if ($exists) {
            throw new \Exception("Tagihan ini sudah di-assign ke siswa tersebut.");
        }

        $jatuhTempo = Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->endOfMonth();

        return TagihanSiswa::create([
            'tagihan_id' => $tagihanId,
            'siswa_id' => $siswaId,
            'status' => 'belum_bayar',
            'jatuh_tempo' => $jatuhTempo
        ]);
    }
}
