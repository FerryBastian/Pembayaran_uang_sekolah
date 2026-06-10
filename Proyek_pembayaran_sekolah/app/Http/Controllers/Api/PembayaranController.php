<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappPembayaranBerhasil;
use App\Models\Pembayaran;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;

class PembayaranController extends Controller
{
    public function callback(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $validated = $request->validate([
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required', 'numeric'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'fraud_status' => ['nullable', 'string'],
            'payment_type' => ['nullable', 'string'],
        ]);

        try {
            if (blank(Config::$serverKey)) {
                Log::error('Midtrans callback gagal: server key belum dikonfigurasi.');

                return response()->json(['message' => 'Konfigurasi Midtrans tidak lengkap'], 500);
            }

            $expectedSignature = hash(
                'sha512',
                $validated['order_id']
                .$validated['status_code']
                .$validated['gross_amount']
                .Config::$serverKey
            );

            if (! hash_equals($expectedSignature, $validated['signature_key'])) {
                Log::warning('Midtrans callback ditolak: signature tidak valid.', [
                    'order_id' => $validated['order_id'],
                ]);

                return response()->json(['message' => 'Signature tidak valid'], 403);
            }

            // Ambil data langsung dari request body
            $orderId = $validated['order_id'];
            $transactionStatus = $validated['transaction_status'];
            $fraudStatus = $validated['fraud_status'] ?? null;
            $paymentType = $validated['payment_type'] ?? null;

            Log::info('Midtrans callback received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
            ]);

            // Cari pembayaran
            $pembayaran = Pembayaran::where('order_id', $orderId)->first();

            if (! $pembayaran) {
                Log::warning('Pembayaran tidak ditemukan untuk order_id: '.$orderId);

                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            $callbackAmount = (int) round((float) $validated['gross_amount'] * 100);
            $storedAmount = (int) round((float) $pembayaran->gross_amount * 100);

            if ($callbackAmount !== $storedAmount) {
                Log::warning('Midtrans callback ditolak: nominal pembayaran tidak cocok.', [
                    'order_id' => $orderId,
                ]);

                return response()->json(['message' => 'Nominal pembayaran tidak cocok'], 422);
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

            $sudahLunas = $pembayaran->transaction_status === 'lunas';

            if ($sudahLunas && $status !== 'lunas') {
                Log::warning('Callback Midtrans diabaikan karena pembayaran sudah lunas.', [
                    'order_id' => $orderId,
                    'incoming_status' => $status,
                ]);

                return response()->json(['message' => 'OK'], 200);
            }

            // Update pembayaran
            $pembayaran->update([
                'transaction_status' => $status,
                'payment_type' => $paymentType ?? $pembayaran->payment_type,
            ]);

            // Update tagihan_siswa
            $statusTagihan = match ($status) {
                'lunas' => 'lunas',
                'pending' => 'pending',
                default => 'belum_bayar',
            };

            $tagihanSiswa = TagihanSiswa::with('siswa.orangTua')
                ->find($pembayaran->tagihan_siswa_id);

            $tagihanSiswa?->update(['status' => $statusTagihan]);

            if (! $sudahLunas && $status === 'lunas') {
                $orangTua = $tagihanSiswa?->siswa?->orangTua;

                if ($orangTua && filled($orangTua->no_wa)) {
                    SendWhatsappPembayaranBerhasil::dispatch($orangTua, $tagihanSiswa);
                }
            }

            Log::info('Status pembayaran updated', [
                'order_id' => $orderId,
                'status' => $status,
            ]);

            return response()->json(['message' => 'OK'], 200);

        } catch (\Throwable $e) {
            Log::error('Midtrans callback error: '.$e->getMessage());

            return response()->json(['message' => 'Error'], 500);
        }
    }
}
