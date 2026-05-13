@extends('layouts.app')

@section('title', 'Tagihan Siswa')
@section('header', 'Tagihan Siswa')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Siswa'], ['label' => 'Tagihan']]" />
@endsection

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <x-stat-card title="Belum Bayar" :value="number_format($statusCounts['belum_bayar'], 0, ',', '.')" description="Tagihan aktif" color="danger" />
            <x-stat-card title="Pending" :value="number_format($statusCounts['pending'], 0, ',', '.')" description="Menunggu konfirmasi" color="warning" />
            <x-stat-card title="Lunas" :value="number_format($statusCounts['lunas'], 0, ',', '.')" description="Sudah dibayar" color="success" />
            <x-stat-card title="Total Belum Bayar" value="Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}" description="{{ $siswa->kelas?->nama_kelas ?? 'Kelas belum diisi' }}" color="primary" />
        </div>

        <x-card title="Daftar Tagihan" subtitle="Semua tagihan yang terdaftar atas nama {{ $siswa->nama }}.">
            <form method="GET" action="{{ route('siswa.tagihan.index') }}" x-ref="filterForm" x-data class="mb-4 grid gap-3 lg:grid-cols-[1fr_14rem_auto]">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border-slate-300 pl-9 text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Cari judul, deskripsi, atau tahun..."
                        x-on:input.debounce.500ms="$refs.filterForm.submit()"
                    >
                </div>

                <select name="status" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua status</option>
                    <option value="belum_bayar" @selected(request('status') === 'belum_bayar')>Belum Bayar</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
                </select>

                @if (request('search') || request('status'))
                    <a href="{{ route('siswa.tagihan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                        Reset
                    </a>
                @endif
            </form>

            @if ($tagihanSiswas->isEmpty())
                <x-empty-state title="Tidak ada tagihan" description="Tagihan Anda akan tampil setelah admin membuat tagihan." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Tagihan</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Periode</th>
                                <th class="px-4 py-3">Jatuh Tempo</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($tagihanSiswas as $row)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $row->tagihan?->judul ?? '-' }}</p>
                                        <p class="mt-1 max-w-md truncate text-xs text-slate-500">{{ $row->tagihan?->deskripsi ?: '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">Rp {{ number_format($row->tagihan?->nominal ?? 0, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $row->tagihan?->bulan ?? '-' }}/{{ $row->tagihan?->tahun ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $row->jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-badge :status="$row->status" /></td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ ($row->pembayaran?->transaction_time ?? $row->pembayaran?->created_at)?->format('d/m/Y H:i') ?? '-' }}</td>
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
