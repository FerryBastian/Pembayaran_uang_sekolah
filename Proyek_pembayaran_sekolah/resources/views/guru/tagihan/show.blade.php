@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('header', 'Detail Tagihan')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Guru'], ['label' => 'Tagihan', 'url' => route('guru.tagihan.index')], ['label' => $tagihan->judul]]" />
@endsection

@section('content')
    @php
        $percent = $tagihan->total_siswa > 0 ? round(($tagihan->lunas_count / $tagihan->total_siswa) * 100) : 0;
    @endphp

    <div class="space-y-6">
        <x-card :title="$tagihan->judul" subtitle="Rp {{ number_format($tagihan->nominal, 0, ',', '.') }} | {{ $months[$tagihan->bulan] ?? $tagihan->bulan }} {{ $tagihan->tahun }}">
            @if ($tagihan->deskripsi)
                <p class="mb-5 text-sm leading-6 text-slate-600">{{ $tagihan->deskripsi }}</p>
            @endif

            <div class="grid gap-4 md:grid-cols-4">
                <x-stat-card title="Total Siswa" :value="$tagihan->total_siswa" color="primary" />
                <x-stat-card title="Lunas" :value="$tagihan->lunas_count" color="success" />
                <x-stat-card title="Pending" :value="$tagihan->pending_count" color="warning" />
                <x-stat-card title="Belum Bayar" :value="$tagihan->belum_bayar_count" color="danger" />
            </div>

            <div class="mt-6">
                <div class="mb-2 flex justify-between text-sm">
                    <span class="text-slate-600">Progress pembayaran</span>
                    <span class="font-bold text-primary">{{ $percent }}%</span>
                </div>
                <div class="h-3 rounded-full bg-slate-100">
                    <div class="h-3 rounded-full bg-primary" style="width: {{ $percent }}%"></div>
                </div>
            </div>
        </x-card>

        <x-card title="Status Pembayaran Siswa" subtitle="Daftar siswa beserta status pembayaran tagihan.">
            <form method="GET" action="{{ route('guru.tagihan.show', $tagihan) }}" x-ref="filterForm" x-data class="mb-4 max-w-xs">
                <select name="status" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua status</option>
                    <option value="belum_bayar" @selected(request('status') === 'belum_bayar')>Belum Bayar</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
                </select>
            </form>

            @if ($tagihanSiswas->isEmpty())
                <x-empty-state title="Tidak ada data" description="Tidak ada siswa dengan filter status yang dipilih." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Siswa</th>
                                <th class="px-4 py-3">Kelas</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Jatuh Tempo</th>
                                <th class="px-4 py-3">Tanggal Bayar</th>
                                <th class="px-4 py-3">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($tagihanSiswas as $row)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $row->siswa?->nama ?? '-' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $row->siswa?->nisn ?? '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $row->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-badge :status="$row->status" /></td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $row->jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ ($row->pembayaran?->transaction_time ?? $row->pembayaran?->created_at)?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $row->pembayaran?->payment_type ? str($row->pembayaran->payment_type)->replace('_', ' ')->title() : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $tagihanSiswas->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection
