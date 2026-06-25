<?php

namespace App\Http\Controllers\Web\OrangTua;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Concerns\ResolvesParentProfile;
use App\Models\Pembayaran;
use App\Models\TagihanSiswa;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'bankTransfer' => config('services.bank_transfer'),
        ]);
    }

    public function uploadBukti(Request $request, TagihanSiswa $tagihanSiswa, NotificationService $notificationService)
    {
        $this->authorizeParentAccess($request, $tagihanSiswa);
        $tagihanSiswa->load(['tagihan', 'siswa', 'pembayaran']);

        if ($tagihanSiswa->status === 'lunas') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $validated = $request->validate([
            'bukti_pembayaran' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $pembayaran = $tagihanSiswa->pembayaran;
        $orderId = $pembayaran?->order_id ?: 'TRF-' . $tagihanSiswa->id . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
        $grossAmount = (int) round((float) $tagihanSiswa->tagihan->nominal);

        if ($pembayaran?->bukti_pembayaran) {
            Storage::disk('local')->delete($pembayaran->bukti_pembayaran);
            Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
        }

        $path = $validated['bukti_pembayaran']->store('bukti-pembayaran');

        Pembayaran::updateOrCreate(
            ['tagihan_siswa_id' => $tagihanSiswa->id],
            [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
                'payment_type' => 'transfer_bank',
                'transaction_status' => 'pending',
                'transaction_time' => now(),
                'bukti_pembayaran' => $path,
                'catatan_verifikasi' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]
        );

        $tagihanSiswa->update(['status' => 'pending']);

        $tagihanSiswa->loadMissing(['tagihan', 'siswa.kelas']);

        $notificationService->sendToAdmins(
            'Bukti Pembayaran Baru',
            sprintf(
                '%s mengupload bukti pembayaran untuk tagihan %s atas nama %s kelas %s sebesar Rp %s. Silakan periksa di halaman pembayaran.',
                $request->user()->name,
                $tagihanSiswa->tagihan?->judul ?? '-',
                $tagihanSiswa->siswa?->nama ?? '-',
                $tagihanSiswa->siswa?->kelas?->nama_kelas ?? '-',
                number_format($grossAmount, 0, ',', '.')
            )
        );

        return redirect()
            ->route('orang-tua.tagihan.index')
            ->with('success', 'Bukti pembayaran berhasil diupload. Status tagihan menunggu verifikasi admin.');
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
