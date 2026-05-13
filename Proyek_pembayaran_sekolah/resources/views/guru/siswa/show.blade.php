@extends('layouts.app')

@section('title', 'Detail Siswa')
@section('header', 'Detail Siswa')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Guru'], ['label' => 'Data Siswa', 'url' => route('guru.siswa.index')], ['label' => $siswa->nama]]" />
@endsection

@section('content')
    @php
        $tagihanRows = $siswa->tagihanSiswas->sortByDesc('created_at');
        $totalTagihan = $tagihanRows->count();
        $totalLunas = $tagihanRows->where('status', 'lunas')->count();
        $totalPending = $tagihanRows->where('status', 'pending')->count();
        $totalBelumBayar = $tagihanRows->where('status', 'belum_bayar')->count();
        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    @endphp

    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <x-card title="{{ $siswa->nama }}" subtitle="NISN {{ $siswa->nisn }}">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kelas</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $siswa->kelas?->nama_kelas ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Wali Kelas</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $siswa->kelas?->wali_kelas ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis Kelamin</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Lahir</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Alamat</dt>
                        <dd class="mt-1 text-slate-700">{{ $siswa->alamat ?: '-' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Orang Tua" subtitle="Kontak penanggung jawab siswa.">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $siswa->orangTua?->nama ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Username</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $siswa->orangTua?->user?->username ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">No. HP</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $siswa->orangTua?->no_hp ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">No. WA</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $siswa->orangTua?->no_wa ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Alamat</dt>
                        <dd class="mt-1 text-slate-700">{{ $siswa->orangTua?->alamat ?: '-' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <x-stat-card title="Total Tagihan" :value="$totalTagihan" color="primary" />
            <x-stat-card title="Lunas" :value="$totalLunas" color="success" />
            <x-stat-card title="Pending" :value="$totalPending" color="warning" />
            <x-stat-card title="Belum Bayar" :value="$totalBelumBayar" color="danger" />
        </div>

        <x-card title="Riwayat Tagihan" subtitle="Status pembayaran siswa ini.">
            @if ($tagihanRows->isEmpty())
                <x-empty-state title="Belum ada tagihan" description="Tagihan siswa akan tampil setelah admin membuat tagihan." />
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
                            @foreach ($tagihanRows as $row)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3 font-semibold text-secondary">{{ $row->tagihan?->judul ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">Rp {{ number_format($row->tagihan?->nominal ?? 0, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        {{ $months[(int) ($row->tagihan?->bulan ?? 0)] ?? '-' }} {{ $row->tagihan?->tahun ?? '' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $row->jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-badge :status="$row->status" /></td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ ($row->pembayaran?->transaction_time ?? $row->pembayaran?->created_at)?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
@endsection
