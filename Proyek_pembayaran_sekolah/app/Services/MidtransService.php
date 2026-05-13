<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production'); // false
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapToken(array $params): string
    {
        try {
            // Debug: pastikan server key ter-load
            if (empty(Config::$serverKey)) {
                throw new Exception('Midtrans Server Key tidak ditemukan. Cek file .env');
            }

            $snapToken = Snap::getSnapToken($params);
            return $snapToken;

        } catch (Exception $e) {
            throw new Exception('Gagal membuat Snap Token: ' . $e->getMessage());
        }
    }
}