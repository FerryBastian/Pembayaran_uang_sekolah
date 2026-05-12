<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $siswaRoute = $this->route('siswa');
        $siswaId = is_object($siswaRoute) ? $siswaRoute->getKey() : $siswaRoute;
        $siswa = is_object($siswaRoute) ? $siswaRoute : \App\Models\Siswa::find($siswaId);
        $userId = $siswa?->user_id;

        return [
            'nisn'          => 'sometimes|string|max:20|unique:siswas,nisn,' . $siswaId,
            'nama'          => 'sometimes|string|max:255',
            'kelas_id'      => 'sometimes|exists:kelas,id',
            'orang_tua_id'  => 'nullable|exists:orang_tuas,id',
            'alamat'        => 'nullable|string',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'tanggal_lahir' => 'sometimes|date',
            'username'      => 'sometimes|string|max:255|unique:users,username,' . $userId,
            'email'         => 'nullable|email|unique:users,email,' . $userId,
            'password'      => 'nullable|string|min:6',
        ];
    }
}
