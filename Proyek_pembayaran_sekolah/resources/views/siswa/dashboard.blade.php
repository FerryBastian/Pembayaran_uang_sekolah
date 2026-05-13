@extends('layouts.app')

@section('title', 'Dashboard Siswa')
@section('header', 'Dashboard Siswa')

@section('content')
    @php
        $statusIcons = [
            'lunas' => ['icon' => 'M5 13l4 4L19 7', 'class' => 'bg-green-50 text-success'],
            'pending' => ['icon' => 'M12 6v6l4 2', 'class' => 'bg-amber-50 text-warning'],
            'belum_bayar' => ['icon' => 'M6 6l12 12M18 6 6 18', 'class' => 'bg-red-50 text-danger'],
        ];
    @endphp

    <div class="space-y-6">
        <x-card title="Halo, {{ $siswa->nama }}!" subtitle="{{ $siswa->kelas?->nama_kelas ?? 'Kelas belum diisi' }} | NISN {{ $siswa->nisn }}">
            <div class="grid gap-4 md:grid-cols-4">
                <x-stat-card title="Belum Bayar" :value="number_format($statusPembayaran['belum_bayar'], 0, ',', '.')" description="Tagihan perlu dibayar" color="danger" />
                <x-stat-card title="Pending" :value="number_format($statusPembayaran['pending'], 0, ',', '.')" description="Menunggu konfirmasi" color="warning" />
                <x-stat-card title="Lunas" :value="number_format($statusPembayaran['lunas'], 0, ',', '.')" description="Tagihan selesai" color="success" />
                <x-stat-card title="Total Belum Bayar" value="Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}" description="Akumulasi tagihan aktif" color="primary" />
            </div>
        </x-card>

        <div class="grid gap-6 xl:grid-cols-[1fr_0.9fr]">
            <x-card title="Tagihan Aktif Bulan Ini" subtitle="Status tagihan periode berjalan.">
                @if ($tagihanBulanIni->isEmpty())
                    <x-empty-state title="Tidak ada tagihan bulan ini" description="Tagihan bulan berjalan akan tampil di sini." />
                @else
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($tagihanBulanIni as $row)
                            @php $visual = $statusIcons[$row->status] ?? $statusIcons['belum_bayar']; @endphp
                            <div class="rounded-xl border border-slate-200 p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate font-bold text-secondary">{{ $row->tagihan?->judul ?? 'Tagihan' }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">Jatuh tempo {{ $row->jatuh_tempo?->format('d/m/Y') ?? '-' }}</p>
                                    </div>
                                    <span class="{{ $visual['class'] }} flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="{{ $visual['icon'] }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>

                                <div class="mt-5 flex flex-wrap items-end justify-between gap-3">
                                    <div>
                                        <p class="text-sm text-slate-500">Nominal</p>
                                        <p class="mt-1 text-xl font-bold text-secondary">Rp {{ number_format($row->tagihan?->nominal ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                    <x-badge :status="$row->status" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card title="Tagihan Perlu Perhatian" subtitle="Daftar tagihan aktif paling dekat jatuh tempo.">
                @if ($tagihanAktif->isEmpty())
                    <x-empty-state title="Tidak ada tagihan aktif" description="Semua tagihan Anda sudah lunas atau belum ada tagihan baru." />
                @else
                    <div class="space-y-3">
                        @foreach ($tagihanAktif as $row)
                            @php $visual = $statusIcons[$row->status] ?? $statusIcons['belum_bayar']; @endphp
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="flex items-start gap-3">
                                    <span class="{{ $visual['class'] }} flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="{{ $visual['icon'] }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-start justify-between gap-2">
                                            <p class="font-semibold text-secondary">{{ $row->tagihan?->judul ?? 'Tagihan' }}</p>
                                            <x-badge :status="$row->status" />
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500">Rp {{ number_format($row->tagihan?->nominal ?? 0, 0, ',', '.') }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Jatuh tempo {{ $row->jatuh_tempo?->format('d/m/Y') ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <x-card title="Riwayat Pembayaran Terbaru" subtitle="Transaksi pembayaran terakhir milik Anda.">
            @if ($pembayaranTerbaru->isEmpty())
                <x-empty-state title="Belum ada pembayaran" description="Riwayat pembayaran akan tampil setelah ada transaksi." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="px-4 py-3">Tagihan</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Metode</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($pembayaranTerbaru as $pembayaran)
                                <tr class="hover:bg-blue-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">{{ $pembayaran->order_id }}</td>
                                    <td class="px-4 py-3">{{ $pembayaran->tagihanSiswa?->tagihan?->judul ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">Rp {{ number_format($pembayaran->gross_amount, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $pembayaran->payment_type ? str($pembayaran->payment_type)->replace('_', ' ')->title() : '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-badge :status="$pembayaran->tagihanSiswa?->status ?? $pembayaran->transaction_status">
                                            {{ str($pembayaran->transaction_status)->replace('_', ' ')->title() }}
                                        </x-badge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ ($pembayaran->transaction_time ?? $pembayaran->created_at)?->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
@endsection
