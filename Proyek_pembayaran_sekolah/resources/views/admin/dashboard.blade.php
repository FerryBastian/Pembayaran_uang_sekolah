@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header', 'Dashboard Admin')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card
                title="Total Siswa"
                :value="number_format($totalSiswa, 0, ',', '.')"
                description="Siswa terdaftar"
                color="primary"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m12 4 9 5-9 5-9-5 9-5Zm-5 8v4c0 1.7 2.2 3 5 3s5-1.3 5-3v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Total Guru"
                :value="number_format($totalGuru, 0, ',', '.')"
                description="Guru terdaftar"
                color="secondary"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 7a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Tagihan Bulan Ini"
                :value="number_format($totalTagihanBulanIni, 0, ',', '.')"
                description="Tagihan dibuat bulan ini"
                color="warning"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M7 3h10a2 2 0 0 1 2 2v16l-3-2-2 2-2-2-2 2-2-2-3 2V5a2 2 0 0 1 2-2Zm3 6h6M10 13h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                title="Terkumpul Bulan Ini"
                value="Rp {{ number_format($totalTerkumpulBulanIni, 0, ',', '.') }}"
                description="Pembayaran lunas bulan ini"
                color="success"
            >
                <x-slot:icon>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 7h16v10H4V7Zm0 3h16M7 15h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
            <x-card title="Pembayaran 6 Bulan Terakhir" subtitle="Total pembayaran berstatus lunas per bulan.">
                <div class="h-80">
                    <canvas id="paymentChart" class="h-full w-full"></canvas>
                </div>
            </x-card>

            <x-card title="Status Tagihan Siswa" subtitle="Ringkasan seluruh status pembayaran.">
                @php
                    $totalStatus = array_sum($statusPembayaran);
                    $statusItems = [
                        ['label' => 'Lunas', 'status' => 'lunas', 'value' => $statusPembayaran['lunas']],
                        ['label' => 'Pending', 'status' => 'pending', 'value' => $statusPembayaran['pending']],
                        ['label' => 'Belum Bayar', 'status' => 'belum_bayar', 'value' => $statusPembayaran['belum_bayar']],
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach ($statusItems as $item)
                        @php
                            $percentage = $totalStatus > 0 ? round(($item['value'] / $totalStatus) * 100) : 0;
                            $barColor = [
                                'lunas' => 'bg-success',
                                'pending' => 'bg-warning',
                                'belum_bayar' => 'bg-danger',
                            ][$item['status']];
                        @endphp

                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <x-badge :status="$item['status']">{{ $item['label'] }}</x-badge>
                                <span class="text-sm font-semibold text-secondary">{{ number_format($item['value'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2">
                <x-card title="Tagihan Terbaru" subtitle="Lima tagihan terakhir beserta progres pembayaran.">
                    @if ($tagihanTerbaru->isEmpty())
                        <x-empty-state
                            title="Belum ada tagihan"
                            description="Tagihan yang dibuat admin akan tampil di bagian ini."
                        />
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                                    <tr>
                                        <th class="px-4 py-3">Judul</th>
                                        <th class="px-4 py-3">Nominal</th>
                                        <th class="px-4 py-3">Periode</th>
                                        <th class="px-4 py-3">Progress</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach ($tagihanTerbaru as $tagihan)
                                        @php
                                            $percentage = $tagihan->total_siswa > 0 ? round(($tagihan->lunas_count / $tagihan->total_siswa) * 100) : 0;
                                        @endphp
                                        <tr class="hover:bg-blue-50">
                                            <td class="px-4 py-3">
                                                <p class="font-semibold text-secondary">{{ $tagihan->judul }}</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ $tagihan->created_at?->format('d/m/Y') }}</p>
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 font-medium text-secondary">
                                                Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3">
                                                {{ \Carbon\Carbon::create(null, $tagihan->bulan, 1)->translatedFormat('F') }} {{ $tagihan->tahun }}
                                            </td>
                                            <td class="min-w-48 px-4 py-3">
                                                <div class="mb-2 flex items-center justify-between text-xs">
                                                    <span class="font-medium text-slate-600">{{ $tagihan->lunas_count }}/{{ $tagihan->total_siswa }} siswa</span>
                                                    <span class="font-semibold text-primary">{{ $percentage }}%</span>
                                                </div>
                                                <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                                    <div class="h-full rounded-full bg-primary" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <a href="{{ route('admin.tagihan.show', $tagihan) }}" class="text-sm font-semibold text-primary hover:text-blue-800">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-card>
            </div>

            <x-card title="Quick Action" subtitle="Akses cepat untuk pekerjaan admin.">
                <div class="space-y-3">
                    <a href="{{ route('admin.tagihan.create') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-secondary transition hover:border-primary hover:bg-blue-50 hover:text-primary">
                        Buat Tagihan
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.siswa.create') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-secondary transition hover:border-primary hover:bg-blue-50 hover:text-primary">
                        Tambah Siswa
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.laporan.export-pdf') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-secondary transition hover:border-primary hover:bg-blue-50 hover:text-primary">
                        Download Laporan
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </x-card>
        </div>

        <x-card title="Pembayaran Terbaru" subtitle="Lima transaksi pembayaran terakhir.">
            @if ($pembayaranTerbaru->isEmpty())
                <x-empty-state
                    title="Belum ada pembayaran"
                    description="Riwayat pembayaran terbaru akan tampil setelah ada transaksi."
                />
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="px-4 py-3">Siswa</th>
                                <th class="px-4 py-3">Tagihan</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Metode</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($pembayaranTerbaru as $pembayaran)
                                <tr class="hover:bg-blue-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">{{ $pembayaran->order_id }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-secondary">{{ $pembayaran->tagihanSiswa?->siswa?->nama ?? '-' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $pembayaran->tagihanSiswa?->siswa?->kelas?->nama_kelas ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">{{ $pembayaran->tagihanSiswa?->tagihan?->judul ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">
                                        Rp {{ number_format($pembayaran->gross_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $pembayaran->payment_type ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-badge :status="$pembayaran->tagihanSiswa?->status ?? $pembayaran->transaction_status" />
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        {{ ($pembayaran->transaction_time ?? $pembayaran->created_at)?->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('paymentChart');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            const chartData = @json($paymentChart);

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Total Pembayaran',
                        data: chartData.values,
                        backgroundColor: '#1e40af',
                        borderRadius: 8,
                        maxBarThickness: 44,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    maximumFractionDigits: 0,
                                }).format(context.parsed.y || 0),
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => new Intl.NumberFormat('id-ID', {
                                    notation: 'compact',
                                    maximumFractionDigits: 1,
                                }).format(value),
                            },
                        },
                    },
                },
            });
        });
    </script>
@endsection
