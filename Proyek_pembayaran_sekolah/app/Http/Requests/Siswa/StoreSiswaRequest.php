<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nisn'           => 'required|string|max:20|unique:siswas,nisn',
            'nama'           => 'required|string|max:255',
            'kelas_id'       => 'required|exists:kelas,id',
            'orang_tua_id'   => 'nullable|exists:orang_tuas,id',
            'alamat'         => 'nullable|string',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'required|date',
            // Data untuk tabel users
            'username'       => 'required|string|unique:users,username',
            'email'          => 'nullable|email|unique:users,email',
            'password'       => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'nisn.unique'          => 'NISN sudah terdaftar.',
            'kelas_id.exists'      => 'Kelas tidak ditemukan.',
            'username.unique'      => 'Username sudah digunakan.',
            'jenis_kelamin.in'     => 'Jenis kelamin harus L atau P.',
        ];
    }
}
