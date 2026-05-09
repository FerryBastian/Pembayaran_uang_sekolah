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
        $query = OrangTua::with(['user', 'siswas']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('no_hp', 'like', '%' . $filters['search'] . '%');
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

            if (!empty($data['password'])) {
                $orangTua->user->update([
                    'password' => Hash::make($data['password']),
                ]);
            }

            if (!empty($data['email'])) {
                $orangTua->user->update(['email' => $data['email']]);
            }

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