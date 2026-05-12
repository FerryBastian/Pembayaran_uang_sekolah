<?php

namespace App\Http\Requests\OrangTua;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrangTuaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $orangTuaRoute = $this->route('orang_tua');
        $orangTuaId = is_object($orangTuaRoute) ? $orangTuaRoute->getKey() : $orangTuaRoute;

        // Cari user_id dari orang tua ini
        $orangTua = is_object($orangTuaRoute) ? $orangTuaRoute : \App\Models\OrangTua::find($orangTuaId);
        $userId   = $orangTua?->user_id;

        return [
            'nama'     => 'sometimes|string|max:255',
            'no_hp'    => 'sometimes|string|max:20|unique:orang_tuas,no_hp,' . $orangTuaId,
            'no_wa'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $userId,
            'email'    => 'nullable|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6',
        ];
    }
}
