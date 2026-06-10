@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('header', 'Detail Tagihan')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Tagihan', 'url' => route('admin.tagihan.index')], ['label' => $tagihan->judul]]" />
@endsection

@section('content')
    @php
        $percent = $tagihan->total_siswa > 0 ? round(($tagihan->lunas_count / $tagihan->total_siswa) * 100) : 0;
        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    @endphp
    <div class="space-y-6">
        @if (session('success')) <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert> @endif
        @if (session('error')) <x-alert type="error" title="Gagal">{{ session('error') }}</x-alert> @endif

        <x-card :title="$tagihan->judul" subtitle="Rp {{ number_format($tagihan->nominal, 0, ',', '.') }} | {{ $months[$tagihan->bulan] }} {{ $tagihan->tahun }}">
            <x-slot:actions>
                <form method="POST" action="{{ route('admin.tagihan.notifikasi.blast', $tagihan) }}">
                    @csrf
                    <button class="rounded-xl bg-success px-4 py-2.5 text-sm font-bold text-white hover:bg-green-700">Kirim Pengingat WA</button>
                </form>
            </x-slot:actions>
            <div class="grid gap-4 md:grid-cols-4">
                <x-stat-card title="Total Siswa" :value="$tagihan->total_siswa" color="primary" />
                <x-stat-card title="Lunas" :value="$tagihan->lunas_count" color="success" />
                <x-stat-card title="Pending" :value="$tagihan->pending_count" color="warning" />
                <x-stat-card title="Belum Bayar" :value="$tagihan->belum_bayar_count" color="danger" />
            </div>
            <div class="mt-6">
                <div class="mb-2 flex justify-between text-sm"><span>Progress pembayaran</span><span class="font-bold text-primary">{{ $percent }}%</span></div>
                <div class="h-3 rounded-full bg-slate-100"><div class="h-3 rounded-full bg-primary" style="width: {{ $percent }}%"></div></div>
            </div>
        </x-card>

        <x-card title="Assign Manual" subtitle="Tambahkan tagihan ke siswa yang belum mendapat tagihan ini.">
            <form method="POST" action="{{ route('admin.tagihan.assign.store', $tagihan) }}" class="flex flex-col gap-3 sm:flex-row">
                @csrf
                <select name="siswa_id" class="flex-1 rounded-xl border-slate-300 text-sm focus:border-primary focus:ring-primary" required>
                    <option value="">Pilih siswa</option>
                    @foreach ($unassignedSiswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->nama }} - {{ $siswa->kelas?->nama_kelas ?? '-' }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white">Assign</button>
            </form>
        </x-card>

        <x-card title="Status Pembayaran Siswa" subtitle="Daftar siswa beserta status pembayaran tagihan.">
            <form method="GET" action="{{ route('admin.tagihan.show', $tagihan) }}" class="mb-4 max-w-xs">
                <select name="status" class="w-full rounded-xl border-slate-300 text-sm focus:border-primary focus:ring-primary" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <option value="belum_bayar" @selected(request('status') === 'belum_bayar')>Belum Bayar</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
                </select>
            </form>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr><th class="px-4 py-3">Siswa</th><th class="px-4 py-3">Kelas</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Jatuh Tempo</th><th class="px-4 py-3">Tanggal Bayar</th><th class="px-4 py-3 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($tagihanSiswas as $row)
                            <tr class="hover:bg-blue-50">
                                <td class="px-4 py-3 font-semibold text-secondary">{{ $row->siswa?->nama ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $row->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                                <td class="px-4 py-3"><x-badge :status="$row->status" /></td>
                                <td class="px-4 py-3">{{ $row->jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3">{{ ($row->pembayaran?->transaction_time ?? $row->pembayaran?->created_at)?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.tagihan-siswa.notifikasi.send', $row) }}">
                                        @csrf
                                        <button class="text-sm font-semibold text-primary hover:text-blue-800">Kirim WA</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $tagihanSiswas->links() }}</div>
        </x-card>
    </div>
@endsection
