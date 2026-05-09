<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuruResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nip'            => $this->nip,
            'nama'           => $this->nama,
            'mata_pelajaran' => $this->mata_pelajaran,
            'alamat'         => $this->alamat,
            'user'           => [
                'id'       => $this->user?->id,
                'username' => $this->user?->username,
                'email'    => $this->user?->email,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}