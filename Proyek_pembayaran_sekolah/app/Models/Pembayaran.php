<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'tagihan_siswa_id', 'order_id', 'gross_amount',
        'payment_type', 'transaction_status',
        'transaction_time', 'bukti_pembayaran',
        'catatan_verifikasi', 'verified_by', 'verified_at',
    ];

    protected $casts = [
        'gross_amount'     => 'decimal:2',
        'transaction_time' => 'datetime',
        'verified_at'      => 'datetime',
    ];

    public function tagihanSiswa()
    {
        return $this->belongsTo(TagihanSiswa::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
