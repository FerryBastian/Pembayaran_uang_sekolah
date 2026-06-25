<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappPembayaranBerhasil;
use App\Models\Pembayaran;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $pembayarans = Pembayaran::with(['tagihanSiswa.tagihan', 'tagihanSiswa.siswa.kelas'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                        ->orWhere('payment_type', 'like', "%{$search}%")
                        ->orWhere('transaction_status', 'like', "%{$search}%")
                        ->orWhereHas('tagihanSiswa.siswa', fn ($siswa) => $siswa->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                        ->orWhereHas('tagihanSiswa.tagihan', fn ($tagihan) => $tagihan->where('judul', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('transaction_status', $request->status))
            ->latest()
            ->paginate(15)
            ->appends($request->only(['search', 'status']));

        $statusOptions = Pembayaran::query()
            ->select('transaction_status')
            ->whereNotNull('transaction_status')
            ->distinct()
            ->orderBy('transaction_status')
            ->pluck('transaction_status');

        return view('admin.pembayaran.index', compact('pembayarans', 'statusOptions'));
    }

    public function bukti(Pembayaran $pembayaran)
    {
        abort_unless($pembayaran->bukti_pembayaran, 404);

        return $this->streamBuktiPembayaran($pembayaran);
    }

    public function verify(Request $request, Pembayaran $pembayaran, NotificationService $notificationService)
    {
        $pembayaran->load('tagihanSiswa.siswa.orangTua');

        if ($pembayaran->transaction_status === 'lunas') {
            return back()->with('success', 'Pembayaran sudah berstatus lunas.');
        }

        $pembayaran->update([
            'transaction_status' => 'lunas',
            'catatan_verifikasi' => null,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $tagihanSiswa = $pembayaran->tagihanSiswa;
        $tagihanSiswa?->update(['status' => 'lunas']);

        $orangTua = $tagihanSiswa?->siswa?->orangTua;

        $notificationService->sendToUser(
            $orangTua?->user_id,
            'Pembayaran Dikonfirmasi',
            $this->paymentConfirmationMessage($pembayaran, 'Pembayaran Anda sudah diverifikasi dan tagihan dinyatakan lunas.')
        );

        if ($orangTua && filled($orangTua->no_wa)) {
            SendWhatsappPembayaranBerhasil::dispatch($orangTua, $tagihanSiswa);
        }

        return back()->with('success', 'Pembayaran berhasil ditandai lunas.');
    }

    public function reject(Request $request, Pembayaran $pembayaran, NotificationService $notificationService)
    {
        $validated = $request->validate([
            'catatan_verifikasi' => ['nullable', 'string', 'max:500'],
        ]);

        $pembayaran->load('tagihanSiswa');

        if ($pembayaran->transaction_status === 'lunas') {
            return back()->with('error', 'Pembayaran yang sudah lunas tidak bisa ditolak.');
        }

        $pembayaran->update([
            'transaction_status' => 'gagal',
            'catatan_verifikasi' => $validated['catatan_verifikasi'] ?: 'Bukti pembayaran ditolak admin.',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $pembayaran->tagihanSiswa?->update(['status' => 'belum_bayar']);

        $pembayaran->loadMissing('tagihanSiswa.siswa.orangTua');

        $notificationService->sendToUser(
            $pembayaran->tagihanSiswa?->siswa?->orangTua?->user_id,
            'Bukti Pembayaran Ditolak',
            $this->paymentConfirmationMessage($pembayaran, $pembayaran->catatan_verifikasi)
        );

        return back()->with('success', 'Pembayaran ditolak. Orang tua dapat upload bukti pembayaran ulang.');
    }

    private function streamBuktiPembayaran(Pembayaran $pembayaran)
    {
        $path = $pembayaran->bukti_pembayaran;
        $disk = Storage::disk('local')->exists($path) ? 'local' : 'public';

        abort_unless(Storage::disk($disk)->exists($path), 404);

        return response()->file(Storage::disk($disk)->path($path));
    }

    private function paymentConfirmationMessage(Pembayaran $pembayaran, string $statusMessage): string
    {
        $pembayaran->loadMissing(['tagihanSiswa.tagihan', 'tagihanSiswa.siswa.kelas']);
        $tagihanSiswa = $pembayaran->tagihanSiswa;

        return sprintf(
            '%s Tagihan %s untuk %s kelas %s sebesar Rp %s. Order ID: %s.',
            $statusMessage,
            $tagihanSiswa?->tagihan?->judul ?? '-',
            $tagihanSiswa?->siswa?->nama ?? '-',
            $tagihanSiswa?->siswa?->kelas?->nama_kelas ?? '-',
            number_format((float) $pembayaran->gross_amount, 0, ',', '.'),
            $pembayaran->order_id
        );
    }
}
