<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanSiswa extends Model
{
    protected $fillable = [
        'tagihan_id', 'siswa_id', 'status', 'jatuh_tempo',
    ];

    protected $casts = [
        'jatuh_tempo' => 'date',
    ];

    public function tagihan()    { return $this->belongsTo(Tagihan::class); }
    public function siswa()      { return $this->belongsTo(Siswa::class); }

    // Relasi 1:1
    public function pembayaran() { return $this->hasOne(Pembayaran::class); }

    // Scope filter status
    public function scopeBelumBayar($q) { return $q->where('status', 'belum_bayar'); }
    public function scopePending($q)    { return $q->where('status', 'pending'); }
    public function scopeLunas($q)      { return $q->where('status', 'lunas'); }
}