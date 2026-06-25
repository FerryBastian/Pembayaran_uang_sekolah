@extends('layouts.app')

@section('title', 'Bayar Tagihan')
@section('header', 'Bayar Tagihan')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Orang Tua'], ['label' => 'Tagihan Anak', 'url' => route('orang-tua.tagihan.index')], ['label' => 'Bayar']]" />
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
            <x-alert type="error" title="Upload gagal">
                {{ $errors->first() }}
            </x-alert>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1fr_0.85fr]">
            <x-card title="{{ $tagihanSiswa->tagihan?->judul ?? 'Tagihan' }}" subtitle="Detail tagihan pembayaran siswa.">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Siswa</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $tagihanSiswa->siswa?->nama ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kelas</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $tagihanSiswa->siswa?->kelas?->nama_kelas ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nominal</dt>
                        <dd class="mt-1 text-2xl font-bold text-secondary">Rp {{ number_format($tagihanSiswa->tagihan?->nominal ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jatuh Tempo</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $tagihanSiswa->jatuh_tempo?->format('d/m/Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</dt>
                        <dd class="mt-2"><x-badge :status="$tagihanSiswa->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Order ID</dt>
                        <dd class="mt-1 font-semibold text-secondary">{{ $tagihanSiswa->pembayaran?->order_id ?? 'Belum dibuat' }}</dd>
                    </div>
                    @if ($tagihanSiswa->tagihan?->deskripsi)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Deskripsi</dt>
                            <dd class="mt-1 text-slate-700">{{ $tagihanSiswa->tagihan->deskripsi }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            <x-card title="Transfer Bank" subtitle="Transfer sesuai nominal tagihan, lalu upload bukti pembayaran.">
                <div class="space-y-5">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nomor Rekening</p>
                        <p class="mt-2 text-2xl font-bold text-secondary">{{ $bankTransfer['account_number'] }}</p>
                        <p class="mt-1 font-semibold text-secondary">{{ $bankTransfer['bank_name'] }} - a.n {{ $bankTransfer['account_holder'] }}</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <p class="text-sm text-slate-500">Total pembayaran</p>
                        <p class="mt-1 text-2xl font-bold text-secondary">Rp {{ number_format($tagihanSiswa->tagihan?->nominal ?? 0, 0, ',', '.') }}</p>
                    </div>

                    @if ($tagihanSiswa->status === 'lunas')
                        <x-alert type="success" title="Tagihan sudah lunas" :dismissible="false">
                            Pembayaran untuk tagihan ini sudah selesai.
                        </x-alert>
                    @else
                        @if ($tagihanSiswa->pembayaran?->bukti_pembayaran && $tagihanSiswa->status === 'pending')
                            <x-alert type="warning" title="Menunggu verifikasi admin" :dismissible="false">
                                Bukti pembayaran sudah diupload. Anda masih bisa mengganti bukti pembayaran selama admin belum menandai tagihan lunas.
                            </x-alert>
                        @endif

                        <form method="POST" action="{{ route('orang-tua.tagihan.upload-bukti', $tagihanSiswa) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label for="bukti_pembayaran" class="block text-sm font-semibold text-secondary">Bukti Pembayaran</label>
                                <input
                                    id="bukti_pembayaran"
                                    name="bukti_pembayaran"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.pdf"
                                    required
                                    class="mt-2 block w-full rounded-xl border border-slate-300 text-sm text-slate-700 file:mr-4 file:border-0 file:bg-primary file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-800"
                                >
                                <p class="mt-2 text-xs leading-5 text-slate-500">Format JPG, PNG, atau PDF. Maksimal 4 MB.</p>
                            </div>

                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-800">
                                Upload Bukti Pembayaran
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('orang-tua.tagihan.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                        Kembali ke Tagihan Anak
                    </a>
                </div>
            </x-card>
        </div>
    </div>
@endsection
