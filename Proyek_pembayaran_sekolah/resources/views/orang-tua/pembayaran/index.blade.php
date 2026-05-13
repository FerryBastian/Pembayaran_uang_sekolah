@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('header', 'Riwayat Pembayaran')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Orang Tua'], ['label' => 'Pembayaran']]" />
@endsection

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <x-stat-card title="Total Transaksi" :value="number_format($jumlahTransaksi, 0, ',', '.')" description="Semua transaksi anak" color="primary" />
            <x-stat-card title="Total Pembayaran" value="Rp {{ number_format($totalPembayaran, 0, ',', '.') }}" description="Nilai transaksi tercatat" color="success" />
            <x-stat-card title="Jumlah Anak" :value="number_format($orangTua->siswas->count(), 0, ',', '.')" description="Terhubung ke akun ini" color="secondary" />
        </div>

        <x-card title="Riwayat Pembayaran Anak" subtitle="Daftar transaksi pembayaran untuk anak yang terhubung dengan akun Anda.">
            <form method="GET" action="{{ route('orang-tua.pembayaran.index') }}" x-ref="filterForm" x-data class="mb-4 grid gap-3 xl:grid-cols-[1fr_14rem_14rem_auto]">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border-slate-300 pl-9 text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Cari order, siswa, tagihan, metode..."
                        x-on:input.debounce.500ms="$refs.filterForm.submit()"
                    >
                </div>

                <select name="siswa_id" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua anak</option>
                    @foreach ($orangTua->siswas as $siswa)
                        <option value="{{ $siswa->id }}" @selected((string) request('siswa_id') === (string) $siswa->id)>{{ $siswa->nama }} - {{ $siswa->kelas?->nama_kelas ?? '-' }}</option>
                    @endforeach
                </select>

                <select name="status" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua status</option>
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>

                @if (request('search') || request('status') || request('siswa_id'))
                    <a href="{{ route('orang-tua.pembayaran.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                        Reset
                    </a>
                @endif
            </form>

            @if ($pembayarans->isEmpty())
                <x-empty-state title="Belum ada pembayaran" description="Riwayat pembayaran akan muncul setelah transaksi dibuat." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
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
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($pembayarans as $pembayaran)
                                <tr class="hover:bg-blue-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">{{ $pembayaran->order_id }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $pembayaran->tagihanSiswa?->siswa?->nama ?? '-' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $pembayaran->tagihanSiswa?->siswa?->kelas?->nama_kelas ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $pembayaran->tagihanSiswa?->tagihan?->judul ?? '-' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $pembayaran->tagihanSiswa?->tagihan?->bulan ?? '-' }}/{{ $pembayaran->tagihanSiswa?->tagihan?->tahun ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">Rp {{ number_format($pembayaran->gross_amount, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $pembayaran->payment_type ? str($pembayaran->payment_type)->replace('_', ' ')->title() : '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-badge :status="$pembayaran->tagihanSiswa?->status ?? $pembayaran->transaction_status">
                                            {{ str($pembayaran->transaction_status)->replace('_', ' ')->title() }}
                                        </x-badge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        {{ ($pembayaran->transaction_time ?? $pembayaran->created_at)?->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $pembayarans->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection
