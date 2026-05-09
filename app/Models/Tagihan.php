<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $fillable = [
        'created_by', 'judul', 'deskripsi', 'nominal', 'bulan', 'tahun',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function pembuatTagihan()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tagihanSiswas()
    {
        return $this->hasMany(TagihanSiswa::class);
    }
}