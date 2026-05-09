<?php

namespace App\Http\Requests\OrangTua;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrangTuaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $orangTuaId = $this->route('orang_tua');

        // Cari user_id dari orang tua ini
        $orangTua = \App\Models\OrangTua::find($orangTuaId);
        $userId   = $orangTua?->user_id;

        return [
            'nama'     => 'sometimes|string|max:255',
            'no_hp'    => 'sometimes|string|max:20|unique:orang_tuas,no_hp,' . $orangTuaId,
            'no_wa'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
            'email'    => 'nullable|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6',
        ];
    }
}