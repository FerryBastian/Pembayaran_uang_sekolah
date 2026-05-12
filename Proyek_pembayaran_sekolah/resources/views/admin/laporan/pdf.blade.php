<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        p { margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; }
        th { background: #e2e8f0; text-align: left; }
        .summary { margin: 12px 0; }
        .summary td { border: none; padding: 3px 8px 3px 0; }
    </style>
</head>
<body>
    <h1>Laporan Pembayaran Uang Sekolah</h1>
    <p>Dicetak: {{ $generatedAt->format('d/m/Y H:i') }}</p>

    <table class="summary">
        <tr>
            <td>Total Data: {{ number_format($summary['total_data'], 0, ',', '.') }}</td>
            <td>Total Tagihan: Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</td>
            <td>Terkumpul: Rp {{ number_format($summary['total_terkumpul'], 0, ',', '.') }}</td>
            <td>Belum Terkumpul: Rp {{ number_format($summary['total_belum_terkumpul'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tagihan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->siswa?->nama ?? '-' }}</td>
                    <td>{{ $row->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                    <td>{{ $row->tagihan?->judul ?? '-' }}</td>
                    <td>Rp {{ number_format($row->tagihan?->nominal ?? 0, 0, ',', '.') }}</td>
                    <td>{{ str($row->status)->replace('_', ' ')->title() }}</td>
                    <td>{{ ($row->pembayaran?->transaction_time ?? $row->pembayaran?->created_at)?->format('d/m/Y H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
