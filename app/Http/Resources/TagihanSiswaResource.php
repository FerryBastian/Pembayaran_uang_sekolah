<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagihanSiswaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'jatuh_tempo' => $this->jatuh_tempo,
            'tagihan' => [
                'id' => $this->tagihan->id,
                'judul' => $this->tagihan->judul,
                'nominal' => 'Rp ' . number_format($this->tagihan->nominal, 0, ',', '.'),
                'nominal_raw' => $this->tagihan->nominal,
                'bulan' => $this->tagihan->bulan,
                'tahun' => $this->tagihan->tahun,
            ],
            'siswa' => [
                'id' => $this->siswa->id,
                'nama' => $this->siswa->nama,
                'nisn' => $this->siswa->nisn,
                'kelas' => $this->siswa->kelas ? $this->siswa->kelas->nama_kelas : null,
            ]
        ];
    }
}