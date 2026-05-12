@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('header', 'Riwayat Pembayaran')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Pembayaran']]" />
@endsection

@section('content')
    <x-card title="Semua Riwayat Pembayaran" subtitle="Pantau seluruh transaksi pembayaran siswa.">
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
                                <td class="px-4 py-3">{{ $pembayaran->tagihanSiswa?->tagihan?->judul ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">Rp {{ number_format($pembayaran->gross_amount, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pembayaran->payment_type ?: '-' }}</td>
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
@endsection
