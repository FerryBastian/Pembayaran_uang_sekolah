<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $kelasId = $this->route('kela'); // Laravel auto singular

        return [
            'nama_kelas' => 'sometimes|string|max:100|unique:kelas,nama_kelas,' . $kelasId,
            'wali_kelas' => 'nullable|string|max:255',
        ];
    }
}