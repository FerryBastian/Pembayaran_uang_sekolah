<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $guruId = $this->route('guru');
        $guru   = \App\Models\Guru::find($guruId);
        $userId = $guru?->user_id;

        return [
            'nip'            => 'sometimes|string|max:30|unique:gurus,nip,' . $guruId,
            'nama'           => 'sometimes|string|max:255',
            'mata_pelajaran' => 'nullable|string|max:255',
            'alamat'         => 'nullable|string',
            'email'          => 'nullable|email|unique:users,email,' . $userId,
            'password'       => 'nullable|string|min:6',
        ];
    }
}