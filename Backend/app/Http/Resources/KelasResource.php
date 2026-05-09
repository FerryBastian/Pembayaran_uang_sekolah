<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KelasResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'nama_kelas'   => $this->nama_kelas,
            'wali_kelas'   => $this->wali_kelas,
            'jumlah_siswa' => $this->siswas_count ?? 0,
            'created_at'   => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}