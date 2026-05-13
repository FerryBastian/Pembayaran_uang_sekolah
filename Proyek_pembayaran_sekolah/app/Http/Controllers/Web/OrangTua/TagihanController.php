<?php

namespace App\Http\Controllers\Web\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\ResolvesParentProfile;
use App\Models\Pembayaran;
use App\Models\TagihanSiswa;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class TagihanController extends Controller
{
    use ResolvesParentProfile;

    public function index(Request $request)
    {
        $orangTua = $this->resolveParentProfile($request->user(), ['siswas.kelas']);

        $siswaIds = $orangTua->siswas->pluck('id');

        $tagihanSiswas = TagihanSiswa::with(['tagihan', 'siswa.kelas', 'pembayaran'])
            ->whereIn('siswa_id', $siswaIds)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('siswa_id'), fn ($query) => $query->where('siswa_id', $request->input('siswa_id')))
            ->latest()
            ->paginate(10)
            ->appends($request->only(['status', 'siswa_id']));

        return view('orang-tua.tagihan.index', compact('orangTua', 'tagihanSiswas'));
    }

    public function bayar(Request $request, TagihanSiswa $tagihanSiswa)
    {
        $this->authorizeParentAccess($request, $tagihanSiswa);

        $tagihanSiswa->load(['tagihan', 'siswa.kelas', 'pembayaran']);

        return view('orang-tua.tagihan.bayar', [
            'tagihanSiswa' => $tagihanSiswa,
            'midtransClientKey' => config('services.midtrans.client_key'),
            'isProduction' => (bool) config('services.midtrans.is_production'),
        ]);
    }

    public function snapToken(Request $request, TagihanSiswa $tagihanSiswa)
    {
        $this->authorizeParentAccess($request, $tagihanSiswa);
        $tagihanSiswa->load(['tagihan', 'siswa', 'pembayaran']);

        if ($tagihanSiswa->status === 'lunas') {
            return response()->json(['message' => 'Tagihan ini sudah lunas.'], 422);
        }

        if (!config('services.midtrans.server_key') || !config('services.midtrans.client_key')) {
            return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap. Isi MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di .env.'], 422);
        }

        $pembayaran = $tagihanSiswa->pembayaran;

        if ($pembayaran?->snap_token) {
            return response()->json([
                'snap_token' => $pembayaran->snap_token,
                'message' => 'Token pembayaran tersedia.',
            ]);
        }

        $orderId = $pembayaran?->order_id ?: 'SPP-' . $tagihanSiswa->id . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
        $grossAmount = (int) round((float) $tagihanSiswa->tagihan->nominal);

        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = (bool) config('services.midtrans.is_production');
            Config::$isSanitized = (bool) config('services.midtrans.is_sanitized');
            Config::$is3ds = (bool) config('services.midtrans.is_3ds');

            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $request->user()->name,
                    'email' => $request->user()->email,
                ],
                'item_details' => [
                    [
                        'id' => 'TAGIHAN-' . $tagihanSiswa->tagihan->id,
                        'price' => $grossAmount,
                        'quantity' => 1,
                        'name' => Str::limit($tagihanSiswa->tagihan->judul, 45, ''),
                    ],
                ],
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Gagal membuat token pembayaran: ' . $exception->getMessage(),
            ], 422);
        }

        $pembayaran = Pembayaran::updateOrCreate(
            ['tagihan_siswa_id' => $tagihanSiswa->id],
            [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'payment_type' => 'midtrans_snap',
                'transaction_status' => 'pending',
                'snap_token' => $snapToken,
            ]
        );

        $tagihanSiswa->update(['status' => 'pending']);

        return response()->json([
            'snap_token' => $pembayaran->snap_token,
            'message' => 'Token pembayaran berhasil dibuat.',
        ]);
    }

    private function authorizeParentAccess(Request $request, TagihanSiswa $tagihanSiswa): void
    {
        $orangTua = $this->resolveParentProfile($request->user());

        abort_unless(
            $orangTua->siswas()->whereKey($tagihanSiswa->siswa_id)->exists(),
            403
        );
    }
}
