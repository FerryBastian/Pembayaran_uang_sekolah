@extends('layouts.app')

@section('title', 'Laporan Pembayaran')
@section('header', 'Laporan Pembayaran')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Laporan']]" />
@endsection

@section('content')
    <div class="space-y-6">
        <x-card title="Filter Laporan" subtitle="Pilih periode, kelas, dan status pembayaran.">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="grid gap-3 md:grid-cols-5">
                <select name="bulan" class="rounded-xl border-slate-300 text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua bulan</option>
                    @foreach ($months as $key => $month)
                        <option value="{{ $key }}" @selected((string) $filters['bulan'] === (string) $key)>{{ $month }}</option>
                    @endforeach
                </select>
                <select name="tahun" class="rounded-xl border-slate-300 text-sm focus:border-primary focus:ring-primary">
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected((string) $filters['tahun'] === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
                <select name="kelas_id" class="rounded-xl border-slate-300 text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua kelas</option>
                    @foreach ($kelasOptions as $kelas)
                        <option value="{{ $kelas->id }}" @selected((string) $filters['kelas_id'] === (string) $kelas->id)>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-xl border-slate-300 text-sm focus:border-primary focus:ring-primary">
                    <option value="">Semua status</option>
                    <option value="belum_bayar" @selected($filters['status'] === 'belum_bayar')>Belum Bayar</option>
                    <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                    <option value="lunas" @selected($filters['status'] === 'lunas')>Lunas</option>
                </select>
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Terapkan</button>
            </form>
            <div class="mt-3 flex justify-end">
                <a href="{{ route('admin.laporan.export-pdf', request()->query()) }}" class="inline-flex rounded-xl bg-danger px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                    Download PDF
                </a>
            </div>
        </x-card>

        <div class="grid gap-4 md:grid-cols-4">
            <x-stat-card title="Total Data" :value="number_format($summary['total_data'], 0, ',', '.')" color="primary" />
            <x-stat-card title="Total Tagihan" value="Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}" color="warning" />
            <x-stat-card title="Terkumpul" value="Rp {{ number_format($summary['total_terkumpul'], 0, ',', '.') }}" color="success" />
            <x-stat-card title="Belum Terkumpul" value="Rp {{ number_format($summary['total_belum_terkumpul'], 0, ',', '.') }}" color="danger" />
        </div>

        <x-card title="Hasil Laporan" subtitle="Daftar siswa dan status pembayaran berdasarkan filter.">
            @if ($rows->isEmpty())
                <x-empty-state title="Tidak ada data" description="Tidak ada data laporan untuk filter yang dipilih." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Nama Siswa</th>
                                <th class="px-4 py-3">Kelas</th>
                                <th class="px-4 py-3">Tagihan</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tanggal Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($rows as $row)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3">{{ $rows->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3 font-semibold text-secondary">{{ $row->siswa?->nama ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row->tagihan?->judul ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">Rp {{ number_format($row->tagihan?->nominal ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3"><x-badge :status="$row->status" /></td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ ($row->pembayaran?->transaction_time ?? $row->pembayaran?->created_at)?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $rows->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection
