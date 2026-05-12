<?php

namespace App\Http\Requests\OrangTua;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrangTuaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama'     => 'required|string|max:255',
            'no_hp'    => 'required|string|max:20|unique:orang_tuas,no_hp',
            'no_wa'    => 'nullable|string|max:20',
            'alamat'   => 'nullable|string',
            'email'    => 'nullable|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
        ];
    }
}