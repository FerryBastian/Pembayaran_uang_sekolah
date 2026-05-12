<?php

namespace App\Repositories;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GuruRepository
{
    public function getAll(array $filters = [])
    {
        $query = Guru::with('user');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('nip', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('mata_pelajaran', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery->where('username', 'like', '%' . $filters['search'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function findById(int $id): Guru
    {
        return Guru::with('user')->findOrFail($id);
    }

    public function create(array $data): Guru
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['nama'],
                'username' => $data['username'],
                'email'    => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'role'     => 'guru',
            ]);

            $guru = Guru::create([
                'user_id'        => $user->id,
                'nip'            => $data['nip'],
                'nama'           => $data['nama'],
                'mata_pelajaran' => $data['mata_pelajaran'] ?? null,
                'alamat'         => $data['alamat'] ?? null,
            ]);

            return $guru->load('user');
        });
    }

    public function update(int $id, array $data): Guru
    {
        return DB::transaction(function () use ($id, $data) {
            $guru = Guru::findOrFail($id);

            $guru->update([
                'nip'            => $data['nip']            ?? $guru->nip,
                'nama'           => $data['nama']           ?? $guru->nama,
                'mata_pelajaran' => $data['mata_pelajaran'] ?? $guru->mata_pelajaran,
                'alamat'         => $data['alamat']         ?? $guru->alamat,
            ]);

            $userData = [
                'name' => $data['nama'] ?? $guru->nama,
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

            $guru->user->update($userData);

            return $guru->load('user');
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $guru = Guru::findOrFail($id);
            $guru->user->delete();
            return true;
        });
    }
}
