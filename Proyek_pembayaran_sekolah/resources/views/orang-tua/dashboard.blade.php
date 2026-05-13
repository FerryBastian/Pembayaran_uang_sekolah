@extends('layouts.app')

@section('title', 'Dashboard Orang Tua')
@section('header', 'Dashboard Orang Tua')

@section('content')
    <div class="space-y-6">
        <x-card title="Halo, {{ $orangTua->nama }}!" subtitle="Tagihan anak Anda:">
            <div class="grid gap-4 md:grid-cols-4">
                <x-stat-card title="Belum Bayar" value="Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}" description="{{ number_format($jumlahBelumBayar, 0, ',', '.') }} tagihan" color="danger" />
                <x-stat-card title="Pending" value="Rp {{ number_format($totalPending, 0, ',', '.') }}" description="{{ number_format($jumlahPending, 0, ',', '.') }} tagihan menunggu konfirmasi" color="warning" />
                <x-stat-card title="Lunas" :value="number_format($jumlahLunas, 0, ',', '.')" description="Tagihan sudah dibayar" color="success" />
                <x-stat-card title="Jumlah Anak" :value="number_format($orangTua->siswas->count(), 0, ',', '.')" description="Terhubung ke akun ini" color="primary" />
            </div>
        </x-card>

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <x-card title="Status Tagihan Anak" subtitle="Ringkasan tagihan terkini per anak.">
                @if ($orangTua->siswas->isEmpty())
                    <x-empty-state title="Belum ada data anak" description="Data anak akan tampil setelah admin menghubungkan siswa ke akun orang tua." />
                @else
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($orangTua->siswas as $siswa)
                            @php
                                $rows = $tagihanPerAnak->get($siswa->id, collect());
                                $belumBayarRows = $rows->where('status', 'belum_bayar');
                                $pendingRows = $rows->where('status', 'pending');
                                $lunasRows = $rows->where('status', 'lunas');
                                $prioritas = $belumBayarRows->sortBy('jatuh_tempo')->first()
                                    ?? $pendingRows->sortBy('jatuh_tempo')->first()
                                    ?? $rows->sortByDesc('created_at')->first();
                                $nominalBelumBayar = $belumBayarRows->sum(fn ($row) => (float) ($row->tagihan?->nominal ?? 0));
                            @endphp

                            <div class="rounded-xl border {{ $belumBayarRows->isNotEmpty() ? 'border-red-200 bg-red-50/40' : 'border-slate-200 bg-white' }} p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate font-bold text-secondary">{{ $siswa->nama }}</h3>
                                        <p class="mt-1 text-sm text-slate-500">{{ $siswa->kelas?->nama_kelas ?? 'Kelas belum diisi' }}</p>
                                    </div>

                                    @if ($belumBayarRows->isNotEmpty())
                                        <x-badge status="belum_bayar">{{ $belumBayarRows->count() }} belum bayar</x-badge>
                                    @elseif ($pendingRows->isNotEmpty())
                                        <x-badge status="pending">{{ $pendingRows->count() }} pending</x-badge>
                                    @else
                                        <x-badge status="lunas">Aman</x-badge>
                                    @endif
                                </div>

                                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Belum Bayar</p>
                                        <p class="mt-1 font-bold text-danger">{{ $belumBayarRows->count() }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pending</p>
                                        <p class="mt-1 font-bold text-warning">{{ $pendingRows->count() }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Lunas</p>
                                        <p class="mt-1 font-bold text-success">{{ $lunasRows->count() }}</p>
                                    </div>
                                </div>

                                <div class="mt-5 border-t border-slate-200 pt-4">
                                    @if ($prioritas)
                                        <p class="text-sm font-semibold text-secondary">{{ $prioritas->tagihan?->judul ?? 'Tagihan' }}</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            Rp {{ number_format($prioritas->tagihan?->nominal ?? 0, 0, ',', '.') }}
                                            @if ($prioritas->jatuh_tempo)
                                                <span class="mx-1">|</span> Jatuh tempo {{ $prioritas->jatuh_tempo->format('d/m/Y') }}
                                            @endif
                                        </p>
                                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                            <x-badge :status="$prioritas->status" />
                                            @if ($belumBayarRows->isNotEmpty())
                                                <a href="{{ route('orang-tua.tagihan.bayar', $belumBayarRows->sortBy('jatuh_tempo')->first()) }}" class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                                                    Bayar Sekarang
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-slate-500">Belum ada tagihan untuk siswa ini.</p>
                                    @endif

                                    @if ($nominalBelumBayar > 0)
                                        <p class="mt-3 text-sm font-semibold text-danger">
                                            Total belum bayar: Rp {{ number_format($nominalBelumBayar, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card title="Tagihan Perlu Perhatian" subtitle="Tagihan belum bayar dan pending terdekat.">
                @if ($tagihanAktif->isEmpty())
                    <x-empty-state title="Tidak ada tagihan aktif" description="Semua tagihan anak sudah lunas atau belum ada tagihan baru." />
                @else
                    <div class="space-y-3">
                        @foreach ($tagihanAktif->take(6) as $row)
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-secondary">{{ $row->tagihan?->judul ?? 'Tagihan' }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $row->siswa?->nama ?? '-' }} | {{ $row->siswa?->kelas?->nama_kelas ?? '-' }}</p>
                                    </div>
                                    <x-badge :status="$row->status" />
                                </div>
                                <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-bold text-secondary">Rp {{ number_format($row->tagihan?->nominal ?? 0, 0, ',', '.') }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Jatuh tempo {{ $row->jatuh_tempo?->format('d/m/Y') ?? '-' }}</p>
                                    </div>

                                    @if ($row->status === 'belum_bayar')
                                        <a href="{{ route('orang-tua.tagihan.bayar', $row) }}" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
@endsection
