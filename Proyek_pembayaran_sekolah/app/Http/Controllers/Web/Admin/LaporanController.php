<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TagihanSiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $rows = $this->baseQuery($filters)->paginate(20)->appends($request->only(['bulan', 'tahun', 'kelas_id', 'status']));
        $summary = $this->summary($filters);

        return view('admin.laporan.index', [
            'rows' => $rows,
            'summary' => $summary,
            'kelasOptions' => Kelas::orderBy('nama_kelas')->get(),
            'months' => $this->months(),
            'years' => range((int) now()->year - 3, (int) now()->year + 1),
            'filters' => $filters,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->filters($request);
        $rows = $this->baseQuery($filters)->get();
        $summary = $this->summary($filters);

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'rows' => $rows,
            'summary' => $summary,
            'filters' => $filters,
            'months' => $this->months(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan-pembayaran-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    private function baseQuery(array $filters)
    {
        return TagihanSiswa::with(['siswa.kelas', 'tagihan', 'pembayaran'])
            ->when($filters['bulan'], fn ($query) => $query->whereHas('tagihan', fn ($tagihan) => $tagihan->where('bulan', $filters['bulan'])))
            ->when($filters['tahun'], fn ($query) => $query->whereHas('tagihan', fn ($tagihan) => $tagihan->where('tahun', $filters['tahun'])))
            ->when($filters['kelas_id'], fn ($query) => $query->whereHas('siswa', fn ($siswa) => $siswa->where('kelas_id', $filters['kelas_id'])))
            ->when($filters['status'], fn ($query) => $query->where('status', $filters['status']))
            ->latest();
    }

    private function summary(array $filters): array
    {
        $query = $this->baseQuery($filters);

        $totalData = (clone $query)->count();
        $totalNominal = (clone $query)->join('tagihans', 'tagihan_siswas.tagihan_id', '=', 'tagihans.id')->sum('tagihans.nominal');
        $totalTerkumpul = (clone $query)->where('status', 'lunas')->join('tagihans', 'tagihan_siswas.tagihan_id', '=', 'tagihans.id')->sum('tagihans.nominal');

        return [
            'total_data' => $totalData,
            'total_nominal' => $totalNominal,
            'total_terkumpul' => $totalTerkumpul,
            'total_belum_terkumpul' => max(0, $totalNominal - $totalTerkumpul),
            'lunas' => (clone $query)->where('status', 'lunas')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'belum_bayar' => (clone $query)->where('status', 'belum_bayar')->count(),
        ];
    }

    private function filters(Request $request): array
    {
        return [
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun', now()->year),
            'kelas_id' => $request->input('kelas_id'),
            'status' => $request->input('status'),
        ];
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
