@extends('layouts.app')

@section('title', 'Notifikasi')
@section('header', 'Notifikasi')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => $roleLabel], ['label' => 'Notifikasi']]" />
@endsection

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        <div class="grid gap-4 md:grid-cols-3">
            <x-stat-card title="Belum Dibaca" :value="number_format($unreadCount, 0, ',', '.')" description="Perlu ditinjau" color="primary" />
            <x-stat-card title="Total Notifikasi" :value="number_format($notifikasis->total(), 0, ',', '.')" description="Semua notifikasi akun ini" color="secondary" />
            <x-stat-card title="Sudah Dibaca" :value="number_format(max($notifikasis->total() - $unreadCount, 0), 0, ',', '.')" description="Notifikasi selesai" color="success" />
        </div>

        <x-card title="Daftar Notifikasi" subtitle="Klik notifikasi untuk menandainya sudah dibaca.">
            <x-slot:actions>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route($routePrefix . '.notifikasi.read-all') }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                            Tandai Semua Sudah Dibaca
                        </button>
                    </form>
                @endif
            </x-slot:actions>

            @if ($notifikasis->isEmpty())
                <x-empty-state title="Belum ada notifikasi" description="Notifikasi baru akan tampil di halaman ini." />
            @else
                <div class="space-y-3">
                    @foreach ($notifikasis as $notifikasi)
                        @php $isUnread = $notifikasi->status === 'belum_dibaca'; @endphp

                        <div class="rounded-xl border {{ $isUnread ? 'border-blue-200 bg-blue-50/50' : 'border-slate-200 bg-white' }} p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold text-secondary">{{ $notifikasi->judul }}</h3>
                                        <x-badge :status="$notifikasi->status" />
                                    </div>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $notifikasi->pesan }}</p>
                                    <p class="mt-3 text-xs font-medium text-slate-500">
                                        {{ $notifikasi->created_at?->format('d/m/Y H:i') ?? '-' }}
                                    </p>
                                </div>

                                @if ($isUnread)
                                    <form method="POST" action="{{ route($routePrefix . '.notifikasi.read', $notifikasi) }}" class="shrink-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                                            Tandai Dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">{{ $notifikasis->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection
