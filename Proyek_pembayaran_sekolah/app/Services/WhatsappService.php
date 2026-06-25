<?php

namespace App\Services;

use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TagihanSiswa;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private const SEND_MESSAGE_ENDPOINT = '/send/message';

    public function __construct(private readonly ?Client $client = null) {}

    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', trim($phone)) ?? '';

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        return $phone;
    }

    public function sendMessage(string $phone, string $message): bool
    {
        $formattedPhone = $this->formatPhone($phone);

        if ($formattedPhone === '') {
            Log::error('Gagal mengirim WhatsApp: nomor tujuan kosong.');

            return false;
        }

        try {
            $options = [
                'headers' => array_filter([
                    'Accept' => 'application/json',
                    'X-Device-Id' => config('gowa.device_id'),
                ]),
                'json' => [
                    'phone' => $formattedPhone,
                    'message' => $message,
                ],
                'timeout' => config('gowa.timeout', 15),
                'connect_timeout' => 5,
            ];

            if (filled(config('gowa.username'))) {
                $options['auth'] = [
                    config('gowa.username'),
                    (string) config('gowa.password'),
                ];
            }

            $response = $this->httpClient()->post(self::SEND_MESSAGE_ENDPOINT, $options);
            $successful = $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;

            if ($successful) {
                Log::info('Pesan WhatsApp berhasil dikirim.', [
                    'phone' => $formattedPhone,
                    'status_code' => $response->getStatusCode(),
                ]);

                return true;
            }

            Log::error('Gagal mengirim WhatsApp: respons GOWA tidak berhasil.', [
                'phone' => $formattedPhone,
                'status_code' => $response->getStatusCode(),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim pesan WhatsApp.', [
                'phone' => $formattedPhone,
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    public function sendTagihanBaru(OrangTua $orangTua, Tagihan $tagihan, Siswa $siswa): void
    {
        if (blank($orangTua->no_wa)) {
            Log::info('Pengiriman tagihan baru dilewati karena nomor WhatsApp kosong.', [
                'orang_tua_id' => $orangTua->id,
                'siswa_id' => $siswa->id,
                'tagihan_id' => $tagihan->id,
            ]);

            return;
        }

        $siswa->loadMissing('kelas');
        $tagihanSiswa = TagihanSiswa::query()
            ->whereBelongsTo($tagihan)
            ->whereBelongsTo($siswa)
            ->first();
        $bulan = Carbon::create()->month((int) $tagihan->bulan)->locale('id')->translatedFormat('F');
        $message = implode("\n", [
            "Halo Bapak/Ibu {$orangTua->nama},",
            'Terdapat tagihan baru untuk putra/putri Anda:',
            "📋 {$tagihan->judul}",
            "👤 Siswa   : {$siswa->nama} ({$siswa->nisn})",
            '🏫 Kelas   : '.($siswa->kelas?->nama_kelas ?? '-'),
            '💰 Nominal : Rp '.number_format((float) $tagihan->nominal, 0, ',', '.'),
            "📅 Bulan   : {$bulan} {$tagihan->tahun}",
            '⏰ Jatuh Tempo: '.($tagihanSiswa?->jatuh_tempo?->translatedFormat('d F Y') ?? '-'),
            'Silakan login ke sistem untuk melakukan pembayaran.',
            config('app.url'),
            'Terima kasih 🙏',
            config('app.name'),
        ]);

        $this->sendWhatsappNotification($orangTua, $message);
    }

    public function sendPembayaranBerhasil(OrangTua $orangTua, TagihanSiswa $tagihanSiswa): void
    {
        if (blank($orangTua->no_wa)) {
            Log::info('Pengiriman konfirmasi pembayaran dilewati karena nomor WhatsApp kosong.', [
                'orang_tua_id' => $orangTua->id,
                'tagihan_siswa_id' => $tagihanSiswa->id,
            ]);

            return;
        }

        $tagihanSiswa->loadMissing(['tagihan', 'siswa.kelas', 'pembayaran']);
        $tagihan = $tagihanSiswa->tagihan;
        $siswa = $tagihanSiswa->siswa;
        $pembayaran = $tagihanSiswa->pembayaran;
        $bulan = Carbon::create()->month((int) $tagihan->bulan)->locale('id')->translatedFormat('F');
        $waktuBayar = $pembayaran?->transaction_time ?? $pembayaran?->updated_at;
        $message = implode("\n", [
            "Halo Bapak/Ibu {$orangTua->nama},",
            '✅ Pembayaran berhasil dikonfirmasi!',
            "📋 {$tagihan->judul}",
            "👤 Siswa   : {$siswa->nama} ({$siswa->nisn})",
            '🏫 Kelas   : '.($siswa->kelas?->nama_kelas ?? '-'),
            '💰 Nominal : Rp '.number_format((float) $tagihan->nominal, 0, ',', '.'),
            "📅 Bulan   : {$bulan} {$tagihan->tahun}",
            '🕐 Waktu   : '.($waktuBayar?->translatedFormat('d F Y H:i') ?? '-'),
            '🔖 Order ID: '.($pembayaran?->order_id ?? '-'),
            'Status pembayaran Anda sudah LUNAS ✅',
            'Terima kasih telah melakukan pembayaran tepat waktu 🙏',
            config('app.name'),
        ]);

        $this->sendWhatsappNotification($orangTua, $message);
    }

    public function sendPengingatTagihan(OrangTua $orangTua, TagihanSiswa $tagihanSiswa): void
    {
        if (blank($orangTua->no_wa)) {
            Log::info('Pengiriman pengingat tagihan dilewati karena nomor WhatsApp kosong.', [
                'orang_tua_id' => $orangTua->id,
                'tagihan_siswa_id' => $tagihanSiswa->id,
            ]);

            return;
        }

        $tagihanSiswa->loadMissing(['tagihan', 'siswa']);
        $tagihan = $tagihanSiswa->tagihan;
        $siswa = $tagihanSiswa->siswa;
        $message = implode("\n", [
            "Halo Bapak/Ibu {$orangTua->nama},",
            '⚠️ Pengingat Tagihan Belum Dibayar',
            "📋 {$tagihan->judul}",
            "👤 Siswa      : {$siswa->nama}",
            '💰 Nominal    : Rp '.number_format((float) $tagihan->nominal, 0, ',', '.'),
            '⏰ Jatuh Tempo: '.($tagihanSiswa->jatuh_tempo?->translatedFormat('d F Y') ?? '-'),
            'Mohon segera lakukan pembayaran sebelum jatuh tempo.',
            config('app.name'),
        ]);

        $this->sendWhatsappNotification($orangTua, $message);
    }

    public function testKirim(string $noHp): bool
    {
        return $this->sendMessage($noHp, 'Test koneksi WhatsApp berhasil ✅');
    }

    private function sendWhatsappNotification(OrangTua $orangTua, string $message): void
    {
        if (! $this->sendMessage((string) $orangTua->no_wa, $message)) {
            throw new \RuntimeException("Gagal mengirim WhatsApp ke orang tua ID {$orangTua->id}.");
        }
    }

    private function httpClient(): Client
    {
        return $this->client ?? new Client([
            'base_uri' => rtrim((string) config('gowa.api_url'), '/').'/',
        ]);
    }
}
