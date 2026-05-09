<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagihanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi,
            'nominal' => 'Rp ' . number_format($this->nominal, 0, ',', '.'),
            'nominal_raw' => $this->nominal,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'total_siswa' => $this->total_siswa ?? 0,
            'sudah_bayar' => $this->sudah_bayar ?? 0,
            'belum_bayar' => $this->belum_bayar ?? 0,
            'pending' => $this->pending ?? 0,
            'created_by' => $this->creator ? $this->creator->name : null,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}