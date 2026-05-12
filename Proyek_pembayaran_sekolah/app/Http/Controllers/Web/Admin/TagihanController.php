<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tagihan\StoreTagihanRequest;
use App\Http\Requests\Tagihan\UpdateTagihanRequest;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TagihanSiswa;
use App\Repositories\TagihanRepository;
use Exception;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function __construct(private readonly TagihanRepository $tagihanRepository)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'bulan', 'tahun']);
        $tagihans = $this->tagihanRepository->getAll(array_filter($filters, fn ($value) => $value !== null && $value !== ''), 10);
        $tagihans->appends($filters);

        return view('admin.tagihan.index', [
            'tagihans' => $tagihans,
            'months' => $this->months(),
            'years' => range((int) now()->year - 2, (int) now()->year + 2),
        ]);
    }

    public function create()
    {
        return redirect()->route('admin.tagihan.index');
    }

    public function store(StoreTagihanRequest $request)
    {
        $this->tagihanRepository->create($request->validated(), $request->user()->id);

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dibuat dan di-assign ke semua siswa.');
    }

    public function show(Request $request, Tagihan $tagihan)
    {
        $tagihan = $this->tagihanRepository->findById($tagihan->id);
        $tagihan->loadCount([
            'tagihanSiswas as total_siswa',
            'tagihanSiswas as lunas_count' => fn ($q) => $q->where('status', 'lunas'),
            'tagihanSiswas as pending_count' => fn ($q) => $q->where('status', 'pending'),
            'tagihanSiswas as belum_bayar_count' => fn ($q) => $q->where('status', 'belum_bayar'),
        ]);

        $tagihanSiswas = TagihanSiswa::with(['siswa.kelas', 'pembayaran'])
            ->where('tagihan_id', $tagihan->id)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->appends($request->only('status'));

        $assignedIds = TagihanSiswa::where('tagihan_id', $tagihan->id)->pluck('siswa_id');
        $unassignedSiswas = Siswa::with('kelas')->whereNotIn('id', $assignedIds)->orderBy('nama')->get();

        return view('admin.tagihan.show', compact('tagihan', 'tagihanSiswas', 'unassignedSiswas'));
    }

    public function edit(Tagihan $tagihan)
    {
        return redirect()->route('admin.tagihan.index');
    }

    public function update(UpdateTagihanRequest $request, Tagihan $tagihan)
    {
        $this->tagihanRepository->update($tagihan->id, $request->validated());

        return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(Tagihan $tagihan)
    {
        try {
            $this->tagihanRepository->delete($tagihan->id);

            return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
        } catch (Exception $exception) {
            return redirect()->route('admin.tagihan.index')->with('error', $exception->getMessage());
        }
    }

    public function assign(Tagihan $tagihan)
    {
        return redirect()->route('admin.tagihan.show', $tagihan);
    }

    public function storeAssign(Request $request, Tagihan $tagihan)
    {
        $validated = $request->validate(['siswa_id' => ['required', 'exists:siswas,id']]);

        try {
            $this->tagihanRepository->assignManual($tagihan->id, $validated['siswa_id']);

            return redirect()->route('admin.tagihan.show', $tagihan)->with('success', 'Tagihan berhasil di-assign ke siswa.');
        } catch (Exception $exception) {
            return redirect()->route('admin.tagihan.show', $tagihan)->with('error', $exception->getMessage());
        }
    }

    public function blastWhatsapp(Tagihan $tagihan)
    {
        return redirect()->route('admin.tagihan.show', $tagihan)->with('success', 'Permintaan blast notifikasi WA dicatat.');
    }

    public function sendWhatsapp(TagihanSiswa $tagihanSiswa)
    {
        return redirect()->route('admin.tagihan.show', $tagihanSiswa->tagihan_id)->with('success', 'Permintaan notifikasi WA dicatat.');
    }

    private function months(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }
}
