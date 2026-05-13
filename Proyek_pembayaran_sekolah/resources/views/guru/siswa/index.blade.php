@extends('layouts.app')

@section('title', 'Data Siswa')
@section('header', 'Data Siswa')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Guru'], ['label' => 'Data Siswa']]" />
@endsection

@section('content')
    <div class="space-y-6">
        <x-card title="Data Siswa" subtitle="Daftar siswa aktif untuk pemantauan guru.">
            <form method="GET" action="{{ route('guru.siswa.index') }}" x-ref="filterForm" x-data class="mb-4 grid gap-3 lg:grid-cols-[1fr_16rem_auto]">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border-slate-300 pl-9 text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Cari NISN, nama, kelas, atau orang tua..."
                        x-on:input.debounce.500ms="$refs.filterForm.submit()"
                    >
                </div>

                <select name="kelas_id" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua kelas</option>
                    @foreach ($kelasOptions as $kelas)
                        <option value="{{ $kelas->id }}" @selected((string) request('kelas_id') === (string) $kelas->id)>{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>

                @if (request('search') || request('kelas_id'))
                    <a href="{{ route('guru.siswa.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                        Reset
                    </a>
                @endif
            </form>

            @if ($siswas->isEmpty())
                <x-empty-state title="Belum ada siswa" description="Data siswa akan tampil setelah admin menambahkan siswa." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">NISN</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Kelas</th>
                                <th class="px-4 py-3">Orang Tua</th>
                                <th class="px-4 py-3">Jenis Kelamin</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($siswas as $siswa)
                                <tr class="hover:bg-blue-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">{{ $siswa->nisn }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $siswa->nama }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $siswa->user?->username ?: '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-badge status="info">{{ $siswa->kelas?->nama_kelas ?? '-' }}</x-badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-700">{{ $siswa->orangTua?->nama ?? '-' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $siswa->orangTua?->no_hp ?? $siswa->orangTua?->no_wa ?? '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('guru.siswa.show', $siswa) }}" class="font-semibold text-primary hover:text-blue-800">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $siswas->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection
