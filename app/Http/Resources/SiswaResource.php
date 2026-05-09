<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nisn'           => $this->nisn,
            'nama'           => $this->nama,
            'alamat'         => $this->alamat,
            'jenis_kelamin'  => $this->jenis_kelamin,
            'tanggal_lahir'  => $this->tanggal_lahir?->format('Y-m-d'),
            'kelas'          => [
                'id'         => $this->kelas?->id,
                'nama_kelas' => $this->kelas?->nama_kelas,
                'wali_kelas' => $this->kelas?->wali_kelas,
            ],
            'orang_tua'      => $this->whenLoaded('orangTua', fn() => [
                'id'   => $this->orangTua?->id,
                'nama' => $this->orangTua?->nama,
                'no_wa'=> $this->orangTua?->no_wa,
            ]),
            'user'           => [
                'id'       => $this->user?->id,
                'username' => $this->user?->username,
                'email'    => $this->user?->email,
            ],
            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}