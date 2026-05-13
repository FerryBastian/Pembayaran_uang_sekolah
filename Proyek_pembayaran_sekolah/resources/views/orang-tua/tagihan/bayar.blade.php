@extends('layouts.app')

@section('title', 'Bayar Tagihan')
@section('header', 'Bayar Tagihan')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Orang Tua'], ['label' => 'Tagihan Anak', 'url' => route('orang-tua.tagihan.index')], ['label' => 'Bayar']]" />
@endsection

@section('content')
    <div
        class="space-y-6"
        x-data="paymentPage({
            snapUrl: '{{ route('orang-tua.tagihan.snap-token', $tagihanSiswa) }}',
            redirectUrl: '{{ route('orang-tua.tagihan.index') }}'
        })"
    >
        <template x-if="alert.message">
            <div
                class="rounded-xl border p-4 text-sm"
                :class="{
                    'border-green-200 bg-green-50 text-green-800': alert.type === 'success',
                    'border-amber-200 bg-amber-50 text-amber-800': alert.type === 'warning',
                    'border-red-200 bg-red-50 text-red-800': alert.type === 'error'
                }"
            >
                <p class="font-semibold" x-text="alert.title"></p>
                <p class="mt-1" x-text="alert.message"></p>
            </div>
        </template>

        @if (!$midtransClientKey)
            <x-alert type="warning" title="Konfigurasi Midtrans belum lengkap">
                MIDTRANS_CLIENT_KEY belum diisi. Tombol pembayaran akan menampilkan error sampai konfigurasi Midtrans dilengkapi.
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

            <x-card title="Pembayaran Midtrans" subtitle="Lanjutkan pembayaran melalui Snap.">
                <div class="space-y-4">
                    @if ($tagihanSiswa->status === 'lunas')
                        <x-alert type="success" title="Tagihan sudah lunas" :dismissible="false">
                            Pembayaran untuk tagihan ini sudah selesai.
                        </x-alert>
                    @else
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Total pembayaran</p>
                            <p class="mt-1 text-2xl font-bold text-secondary">Rp {{ number_format($tagihanSiswa->tagihan?->nominal ?? 0, 0, ',', '.') }}</p>
                        </div>

                        <button
                            type="button"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                            x-on:click="pay"
                            x-bind:disabled="loading"
                        >
                            <span x-show="!loading">Bayar via Midtrans</span>
                            <span x-show="loading">Menyiapkan pembayaran...</span>
                        </button>

                        <p class="text-xs leading-5 text-slate-500">
                            Setelah popup Midtrans terbuka, selesaikan pembayaran sesuai metode yang dipilih. Status tagihan akan diperbarui setelah transaksi diproses.
                        </p>
                    @endif

                    <a href="{{ route('orang-tua.tagihan.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                        Kembali ke Tagihan Anak
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $midtransClientKey }}"></script>
    <script>
        function paymentPage(config) {
            return {
                loading: false,
                alert: {
                    type: '',
                    title: '',
                    message: '',
                },
                setAlert(type, title, message) {
                    this.alert = { type, title, message };
                },
                async pay() {
                    this.loading = true;
                    this.alert = { type: '', title: '', message: '' };

                    try {
                        const response = await window.axios.post(config.snapUrl);
                        const token = response.data.snap_token;

                        if (!window.snap) {
                            this.setAlert('error', 'Snap belum siap', 'Script Midtrans Snap belum berhasil dimuat.');
                            return;
                        }

                        window.snap.pay(token, {
                            onSuccess: () => {
                                this.setAlert('success', 'Pembayaran berhasil', 'Transaksi berhasil diproses. Halaman akan dimuat ulang.');
                                setTimeout(() => window.location.href = config.redirectUrl, 1200);
                            },
                            onPending: () => {
                                this.setAlert('warning', 'Pembayaran pending', 'Transaksi menunggu penyelesaian pembayaran.');
                                setTimeout(() => window.location.reload(), 1200);
                            },
                            onError: () => {
                                this.setAlert('error', 'Pembayaran gagal', 'Terjadi kesalahan saat memproses pembayaran.');
                            },
                            onClose: () => {
                                this.setAlert('warning', 'Pembayaran ditutup', 'Popup pembayaran ditutup sebelum transaksi selesai.');
                            },
                        });
                    } catch (error) {
                        this.setAlert('error', 'Gagal menyiapkan pembayaran', error.response?.data?.message || 'Terjadi kesalahan saat mengambil token pembayaran.');
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
@endsection
