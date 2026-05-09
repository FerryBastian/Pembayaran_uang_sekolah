<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrangTuaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'nama'    => $this->nama,
            'no_hp'   => $this->no_hp,
            'no_wa'   => $this->no_wa,
            'alamat'  => $this->alamat,
            'user'    => [
                'id'       => $this->user?->id,
                'username' => $this->user?->username,
                'email'    => $this->user?->email,
            ],
            'anak'    => $this->whenLoaded('siswas', fn() =>
                $this->siswas->map(fn($s) => [
                    'id'         => $s->id,
                    'nama'       => $s->nama,
                    'nisn'       => $s->nisn,
                    'kelas'      => $s->kelas?->nama_kelas,
                ])
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}