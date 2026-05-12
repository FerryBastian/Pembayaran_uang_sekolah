<?php

namespace App\Repositories;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SiswaRepository
{
    public function getAll(array $filters = [])
    {
        $query = Siswa::with(['user', 'kelas', 'orangTua']);

        // Filter by kelas
        if (!empty($filters['kelas_id'])) {
            $query->where('kelas_id', $filters['kelas_id']);
        }

        // Search by nama atau nisn
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('nisn', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('kelas', fn ($kelasQuery) => $kelasQuery->where('nama_kelas', 'like', '%' . $filters['search'] . '%'))
                    ->orWhereHas('orangTua', fn ($orangTuaQuery) => $orangTuaQuery->where('nama', 'like', '%' . $filters['search'] . '%'))
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery->where('username', 'like', '%' . $filters['search'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function findById(int $id): ?Siswa
    {
        return Siswa::with(['user', 'kelas', 'orangTua'])->findOrFail($id);
    }

    public function create(array $data): Siswa
    {
        return DB::transaction(function () use ($data) {
            // Buat user account dulu
            $user = User::create([
                'name'     => $data['nama'],
                'username' => $data['username'],
                'email'    => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'role'     => 'siswa',
            ]);

            // Buat data siswa
            $siswa = Siswa::create([
                'user_id'       => $user->id,
                'kelas_id'      => $data['kelas_id'],
                'orang_tua_id'  => $data['orang_tua_id'] ?? null,
                'nisn'          => $data['nisn'],
                'nama'          => $data['nama'],
                'alamat'        => $data['alamat'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tanggal_lahir' => $data['tanggal_lahir'],
            ]);

            return $siswa->load(['user', 'kelas', 'orangTua']);
        });
    }

    public function update(int $id, array $data): Siswa
    {
        return DB::transaction(function () use ($id, $data) {
            $siswa = Siswa::findOrFail($id);

            // Update data siswa
            $siswa->update([
                'kelas_id'      => $data['kelas_id']      ?? $siswa->kelas_id,
                'orang_tua_id'  => $data['orang_tua_id']  ?? $siswa->orang_tua_id,
                'nisn'          => $data['nisn']           ?? $siswa->nisn,
                'nama'          => $data['nama']           ?? $siswa->nama,
                'alamat'        => $data['alamat']         ?? $siswa->alamat,
                'jenis_kelamin' => $data['jenis_kelamin']  ?? $siswa->jenis_kelamin,
                'tanggal_lahir' => $data['tanggal_lahir']  ?? $siswa->tanggal_lahir,
            ]);

            $userData = [
                'name' => $data['nama'] ?? $siswa->nama,
            ];

            if (array_key_exists('username', $data)) {
                $userData['username'] = $data['username'];
            }

            if (array_key_exists('email', $data)) {
                $userData['email'] = $data['email'];
            }

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $siswa->user->update($userData);

            return $siswa->load(['user', 'kelas', 'orangTua']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $siswa = Siswa::findOrFail($id);
            // Hapus user juga (cascade)
            $siswa->user->delete();
            return true;
        });
    }
}
