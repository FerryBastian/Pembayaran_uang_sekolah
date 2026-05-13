<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use Midtrans\Config;

class PembayaranController extends Controller
{
    public function callback(Request $request)
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        try {
            // Ambil data langsung dari request body
            $orderId           = $request->order_id;
            $transactionStatus = $request->transaction_status;
            $fraudStatus       = $request->fraud_status;
            $paymentType       = $request->payment_type;

            \Log::info('Midtrans callback received', $request->all());

            // Cari pembayaran
            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (!$pembayaran) {
                \Log::warning('Pembayaran tidak ditemukan untuk order_id: ' . $orderId);
                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            // Tentukan status
            if ($transactionStatus == 'capture') {
                $status = ($fraudStatus == 'accept') ? 'lunas' : 'gagal';
            } elseif ($transactionStatus == 'settlement') {
                $status = 'lunas';
            } elseif ($transactionStatus == 'pending') {
                $status = 'pending';
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $status = 'gagal';
            } else {
                $status = 'pending';
            }

            // Update pembayaran
            $pembayaran->update([
                'transaction_status' => $status,
                'payment_type'       => $paymentType ?? $pembayaran->payment_type,
            ]);

            // Update tagihan_siswa
            $statusTagihan = match($status) {
                'lunas'  => 'lunas',
                'pending' => 'pending',
                default  => 'belum_bayar',
            };

            TagihanSiswa::where('id', $pembayaran->tagihan_siswa_id)
                ->update(['status' => $statusTagihan]);

            \Log::info('Status pembayaran updated', [
                'order_id' => $orderId,
                'status'   => $status,
            ]);

            return response()->json(['message' => 'OK'], 200);

        } catch (\Exception $e) {
            \Log::error('Midtrans callback error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }
}