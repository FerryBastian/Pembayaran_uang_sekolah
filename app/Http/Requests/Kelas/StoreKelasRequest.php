<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_kelas'  => 'required|string|max:100|unique:kelas,nama_kelas',
            'wali_kelas'  => 'nullable|string|max:255',
        ];
    }
}