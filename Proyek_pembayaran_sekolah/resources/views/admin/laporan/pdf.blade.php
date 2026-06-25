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
        .letterhead { border-bottom: 3px solid #0f172a; margin-bottom: 14px; padding-bottom: 8px; }
        .letterhead-table td { border: none; padding: 0; vertical-align: middle; }
        .letterhead-logo { width: 96px; text-align: center; }
        .letterhead-logo img { width: 82px; height: 82px; object-fit: contain; }
        .letterhead-text { color: #073c87; font-family: DejaVu Sans, sans-serif; text-align: center; text-transform: uppercase; }
        .foundation { font-size: 9px; font-weight: 700; line-height: 1.1; }
        .title-main { font-size: 24px; font-weight: 800; line-height: 1.05; }
        .title-school { font-size: 17px; font-weight: 800; line-height: 1.08; }
        .majors { font-size: 8px; font-weight: 700; line-height: 1.25; margin-top: 5px; text-transform: none; }
        .contact { font-size: 8px; font-weight: 700; line-height: 1.25; margin-top: 2px; text-transform: none; }
        .report-title { margin-top: 8px; text-align: center; }
        .signature { margin-top: 64px; margin-left: auto; width: 240px; text-align: center; }
        .signature-place { margin-bottom: 6px; }
        .signature-space { height: 58px; }
        .signature-name { font-weight: 700; padding-bottom: 2px; }
        .signature-role { margin-top: 3px; }
    </style>
</head>
<body>
    <div class="letterhead">
        <table class="letterhead-table">
            <tr>
                <td class="letterhead-logo">
                    <img src="{{ public_path('images/logo.jpeg') }}" alt="Logo SMK GKPI 1">
                </td>
                <td class="letterhead-text">
                    <div class="foundation">Yayasan Pendidikan Terang Hidup</div>
                    <div class="title-main">Yayasan Pendidikan Terang Hidup</div>
                    <div class="title-school">Sekolah Menengah Kejuruan (SMK) Swasta GKPI 1</div>
                    <div class="title-school">Kelompok Teknologi Rekayasa/TIK dan Kesehatan</div>
                    <div class="majors">
                        Jurusan 1. Teknik Kendaraan Ringan 2. Teknik Distribusi Tenaga Listrik 3. Teknik Sepeda Motor 4. Teknik Komputer Jaringan 5. Keperawatan
                    </div>
                    <div class="contact">
                        Jl. D. I Panjaitan No. 47 NH Pematangsiantar &nbsp;&nbsp; Email : smkgkpi@gmail.com &nbsp;&nbsp; Website : www.smkgkpisiantar.sch.id
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        <h1>Laporan Pembayaran Uang Sekolah</h1>
        <p>Dicetak: {{ $generatedAt->format('d/m/Y H:i') }}</p>
    </div>

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

    <div class="signature">
        <div class="signature-place">Pematangsiantar, {{ $generatedAt->translatedFormat('d F Y') }}</div>
        <div>Admin</div>
        <div class="signature-space"></div>
        <div class="signature-name">{{ $printedBy?->name ?? 'Admin' }}</div>
        <!-- <div class="signature-role">Pengelola Sistem Pembayaran</div> -->
    </div>
</body>
</html>
