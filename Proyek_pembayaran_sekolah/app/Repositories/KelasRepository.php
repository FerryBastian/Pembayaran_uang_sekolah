<?php

namespace App\Repositories;

use App\Models\Kelas;

class KelasRepository
{
    public function getAll(array $filters = [])
    {
        $query = Kelas::withCount('siswas');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama_kelas', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('wali_kelas', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function findById(int $id): Kelas
    {
        return Kelas::withCount('siswas')->findOrFail($id);
    }

    public function create(array $data): Kelas
    {
        return Kelas::create([
            'nama_kelas' => $data['nama_kelas'],
            'wali_kelas' => $data['wali_kelas'] ?? null,
        ]);
    }

    public function update(int $id, array $data): Kelas
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->update($data);
        return $kelas->loadCount('siswas');
    }

    public function delete(int $id): bool
    {
        $kelas = Kelas::findOrFail($id);

        if ($kelas->siswas()->count() > 0) {
            throw new \Exception('Kelas tidak bisa dihapus karena masih memiliki siswa.');
        }

        return $kelas->delete();
    }
}
