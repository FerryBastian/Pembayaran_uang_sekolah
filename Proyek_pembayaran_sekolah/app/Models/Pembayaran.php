<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'tagihan_siswa_id', 'order_id', 'gross_amount',
        'payment_type', 'transaction_status',
        'transaction_time', 'snap_token',
    ];

    protected $casts = [
        'gross_amount'     => 'decimal:2',
        'transaction_time' => 'datetime',
    ];

    public function tagihanSiswa()
    {
        return $this->belongsTo(TagihanSiswa::class);
    }
}