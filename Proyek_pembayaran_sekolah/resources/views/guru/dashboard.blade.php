@extends('layouts.app')

@section('title', 'Dashboard Guru')
@section('header', 'Dashboard Guru')

@section('content')
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <x-stat-card title="Total Siswa" :value="number_format($totalSiswa, 0, ',', '.')" description="Siswa terdaftar" color="primary" />
            <x-stat-card title="Total Kelas" :value="number_format($totalKelas, 0, ',', '.')" description="Kelas aktif" color="secondary" />
            <x-stat-card title="Tagihan Bulan Ini" :value="number_format($tagihanBulanIni, 0, ',', '.')" description="Tagihan periode berjalan" color="warning" />
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <x-card title="Status Pembayaran" subtitle="Ringkasan status seluruh tagihan siswa.">
                @php
                    $totalStatus = array_sum($statusPembayaran);
                    $items = [
                        ['label' => 'Lunas', 'status' => 'lunas', 'value' => $statusPembayaran['lunas']],
                        ['label' => 'Pending', 'status' => 'pending', 'value' => $statusPembayaran['pending']],
                        ['label' => 'Belum Bayar', 'status' => 'belum_bayar', 'value' => $statusPembayaran['belum_bayar']],
                    ];
                @endphp
                <div class="space-y-4">
                    @foreach ($items as $item)
                        @php
                            $percentage = $totalStatus > 0 ? round(($item['value'] / $totalStatus) * 100) : 0;
                            $bar = ['lunas' => 'bg-success', 'pending' => 'bg-warning', 'belum_bayar' => 'bg-danger'][$item['status']];
                        @endphp
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <x-badge :status="$item['status']">{{ $item['label'] }}</x-badge>
                                <span class="text-sm font-semibold text-secondary">{{ number_format($item['value'], 0, ',', '.') }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="{{ $bar }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>

            <x-card title="Ringkasan Kelas" subtitle="Jumlah siswa per kelas.">
                @if ($kelasRingkas->isEmpty())
                    <x-empty-state title="Belum ada kelas" description="Data kelas akan tampil setelah dibuat admin." />
                @else
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($kelasRingkas as $kelas)
                            <div class="rounded-xl border border-slate-200 p-4">
                                <p class="font-bold text-secondary">{{ $kelas->nama_kelas }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $kelas->wali_kelas ?: 'Wali kelas belum diisi' }}</p>
                                <p class="mt-3 text-sm font-semibold text-primary">{{ number_format($kelas->siswas_count, 0, ',', '.') }} siswa</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <x-card title="Tagihan Terbaru" subtitle="Daftar tagihan terbaru untuk pemantauan guru.">
            @if ($tagihanTerbaru->isEmpty())
                <x-empty-state title="Belum ada tagihan" description="Tagihan yang dibuat admin akan tampil di sini." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Judul</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Periode</th>
                                <th class="px-4 py-3">Progress</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($tagihanTerbaru as $tagihan)
                                @php $percentage = $tagihan->total_siswa > 0 ? round(($tagihan->lunas_count / $tagihan->total_siswa) * 100) : 0; @endphp
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3 font-semibold text-secondary">{{ $tagihan->judul }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $tagihan->bulan }}/{{ $tagihan->tahun }}</td>
                                    <td class="min-w-48 px-4 py-3">
                                        <div class="mb-2 flex justify-between text-xs">
                                            <span>{{ $tagihan->lunas_count }}/{{ $tagihan->total_siswa }} siswa</span>
                                            <span class="font-bold text-primary">{{ $percentage }}%</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-primary" style="width: {{ $percentage }}%"></div></div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('guru.tagihan.show', $tagihan) }}" class="font-semibold text-primary hover:text-blue-800">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
@endsection
