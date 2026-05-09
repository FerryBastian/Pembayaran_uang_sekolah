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
        $siswaId = $this->route('siswa');

        return [
            'nisn'          => 'sometimes|string|max:20|unique:siswas,nisn,' . $siswaId,
            'nama'          => 'sometimes|string|max:255',
            'kelas_id'      => 'sometimes|exists:kelas,id',
            'orang_tua_id'  => 'nullable|exists:orang_tuas,id',
            'alamat'        => 'nullable|string',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'tanggal_lahir' => 'sometimes|date',
            'password'      => 'nullable|string|min:6',
        ];
    }
}