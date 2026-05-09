<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nip'            => 'required|string|max:30|unique:gurus,nip',
            'nama'           => 'required|string|max:255',
            'mata_pelajaran' => 'nullable|string|max:255',
            'alamat'         => 'nullable|string',
            'username'       => 'required|string|unique:users,username',
            'email'          => 'nullable|email|unique:users,email',
            'password'       => 'required|string|min:6',
        ];
    }
}