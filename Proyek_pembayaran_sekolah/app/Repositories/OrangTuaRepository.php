<?php

namespace App\Repositories;

use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrangTuaRepository
{
    public function getAll(array $filters = [])
    {
        $query = OrangTua::with('user')->withCount('siswas');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('no_hp', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('no_wa', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('user', function ($userQuery) use ($filters) {
                        $userQuery->where('username', 'like', '%' . $filters['search'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function findById(int $id): OrangTua
    {
        return OrangTua::with(['user', 'siswas.kelas'])->findOrFail($id);
    }

    public function create(array $data): OrangTua
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['nama'],
                'username' => $data['username'],
                'email'    => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'role'     => 'orang_tua',
            ]);

            $orangTua = OrangTua::create([
                'user_id' => $user->id,
                'nama'    => $data['nama'],
                'no_hp'   => $data['no_hp'],
                'no_wa'   => $data['no_wa'] ?? $data['no_hp'],
                'alamat'  => $data['alamat'] ?? null,
            ]);

            return $orangTua->load(['user', 'siswas']);
        });
    }

    public function update(int $id, array $data): OrangTua
    {
        return DB::transaction(function () use ($id, $data) {
            $orangTua = OrangTua::findOrFail($id);

            $orangTua->update([
                'nama'   => $data['nama']   ?? $orangTua->nama,
                'no_hp'  => $data['no_hp']  ?? $orangTua->no_hp,
                'no_wa'  => $data['no_wa']  ?? $orangTua->no_wa,
                'alamat' => $data['alamat'] ?? $orangTua->alamat,
            ]);

            $userData = [
                'name' => $data['nama'] ?? $orangTua->nama,
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

            $orangTua->user->update($userData);

            return $orangTua->load(['user', 'siswas']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $orangTua = OrangTua::findOrFail($id);
            $orangTua->user->delete();
            return true;
        });
    }
}
