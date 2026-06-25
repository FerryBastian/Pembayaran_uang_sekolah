@extends('layouts.app')

@section('title', 'Tagihan Anak')
@section('header', 'Tagihan Anak')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Orang Tua'], ['label' => 'Tagihan Anak']]" />
@endsection

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" title="Gagal">{{ session('error') }}</x-alert>
        @endif

        <x-card title="Tagihan Anak" subtitle="Daftar tagihan untuk anak yang terhubung dengan akun Anda.">
            <form method="GET" action="{{ route('orang-tua.tagihan.index') }}" x-ref="filterForm" x-data class="mb-4 grid gap-3 lg:grid-cols-[1fr_14rem_auto]">
                <select name="siswa_id" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua anak</option>
                    @foreach ($orangTua->siswas as $siswa)
                        <option value="{{ $siswa->id }}" @selected((string) request('siswa_id') === (string) $siswa->id)>{{ $siswa->nama }} - {{ $siswa->kelas?->nama_kelas ?? '-' }}</option>
                    @endforeach
                </select>

                <select name="status" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua status</option>
                    <option value="belum_bayar" @selected(request('status') === 'belum_bayar')>Belum Bayar</option>
                    <option value="pending" @selected(request('status') === 'pending')>Menunggu Verifikasi</option>
                    <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
                </select>

                @if (request('siswa_id') || request('status'))
                    <a href="{{ route('orang-tua.tagihan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                        Reset
                    </a>
                @endif
            </form>

            @if ($tagihanSiswas->isEmpty())
                <x-empty-state title="Tidak ada tagihan" description="Tagihan anak akan tampil setelah admin membuat tagihan." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Anak</th>
                                <th class="px-4 py-3">Tagihan</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Jatuh Tempo</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($tagihanSiswas as $row)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $row->siswa?->nama ?? '-' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $row->siswa?->kelas?->nama_kelas ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $row->tagihan?->judul ?? '-' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $row->tagihan?->bulan }}/{{ $row->tagihan?->tahun }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">Rp {{ number_format($row->tagihan?->nominal ?? 0, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $row->jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-badge :status="$row->status" /></td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($row->status === 'lunas')
                                            <span class="text-sm font-semibold text-success">Selesai</span>
                                        @elseif ($row->status === 'pending')
                                            <a href="{{ route('orang-tua.tagihan.bayar', $row) }}" class="font-semibold text-warning hover:text-amber-700">Ganti Bukti</a>
                                        @else
                                            <a href="{{ route('orang-tua.tagihan.bayar', $row) }}" class="font-semibold text-primary hover:text-blue-800">Bayar Sekarang</a>
                                        @endif
                                    </td>
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
