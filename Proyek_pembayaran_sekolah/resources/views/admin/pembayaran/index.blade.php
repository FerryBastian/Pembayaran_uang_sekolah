@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('header', 'Riwayat Pembayaran')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Pembayaran']]" />
@endsection

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" title="Gagal">{{ session('error') }}</x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="error" title="Validasi gagal">{{ $errors->first() }}</x-alert>
        @endif

    <x-card title="Semua Riwayat Pembayaran" subtitle="Pantau bukti transfer dan verifikasi pembayaran siswa.">
        <form method="GET" action="{{ route('admin.pembayaran.index') }}" x-ref="filterForm" x-data class="mb-4 grid gap-3 lg:grid-cols-[1fr_14rem_auto]">
            <input
                name="search"
                value="{{ request('search') }}"
                class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                placeholder="Cari order, siswa, tagihan, metode..."
                x-on:input.debounce.500ms="$refs.filterForm.submit()"
            >
            <select name="status" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                <option value="">Semua status</option>
                @foreach ($statusOptions as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            @if (request('search') || request('status'))
                <a href="{{ route('admin.pembayaran.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">Reset</a>
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
                            <th class="px-4 py-3">Bukti</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
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
                                <td class="px-4 py-3">{{ $pembayaran->tagihanSiswa?->tagihan?->judul ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">Rp {{ number_format($pembayaran->gross_amount, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pembayaran->payment_type ? str($pembayaran->payment_type)->replace('_', ' ')->title() : '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if ($pembayaran->bukti_pembayaran)
                                        <a href="{{ route('admin.pembayaran.bukti', $pembayaran) }}" target="_blank" class="font-semibold text-primary hover:text-blue-800">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <x-badge :status="$pembayaran->tagihanSiswa?->status ?? $pembayaran->transaction_status" />
                                    @if ($pembayaran->catatan_verifikasi)
                                        <p class="mt-1 max-w-56 whitespace-normal text-xs text-slate-500">{{ $pembayaran->catatan_verifikasi }}</p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    {{ ($pembayaran->transaction_time ?? $pembayaran->created_at)?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($pembayaran->transaction_status === 'pending')
                                        <div class="flex min-w-72 flex-col items-end gap-2">
                                            <form method="POST" action="{{ route('admin.pembayaran.verify', $pembayaran) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-success px-3 py-2 text-xs font-bold text-white hover:bg-green-700">
                                                    Tandai Lunas
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.pembayaran.reject', $pembayaran) }}" class="flex w-full items-center justify-end gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input
                                                    name="catatan_verifikasi"
                                                    class="w-44 rounded-xl border-slate-300 text-xs shadow-sm focus:border-primary focus:ring-primary"
                                                    placeholder="Catatan penolakan"
                                                >
                                                <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-200 px-3 py-2 text-xs font-bold text-danger hover:bg-red-50">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
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
