<?php

namespace App\Http\Requests\Tagihan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'nominal' => 'sometimes|required|numeric|min:0',
            'bulan' => 'sometimes|required|integer|min:1|max:12',
            'tahun' => 'sometimes|required|integer|min:2000',
        ];
    }
}