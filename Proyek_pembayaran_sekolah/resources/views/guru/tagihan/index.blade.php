@extends('layouts.app')

@section('title', 'Data Tagihan')
@section('header', 'Data Tagihan')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Guru'], ['label' => 'Tagihan']]" />
@endsection

@section('content')
    <div class="space-y-6">
        <x-card title="Data Tagihan" subtitle="Daftar tagihan sekolah dan progres pembayaran siswa.">
            <form method="GET" action="{{ route('guru.tagihan.index') }}" x-ref="filterForm" x-data class="mb-4 grid gap-3 lg:grid-cols-[1fr_12rem_10rem_auto]">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border-slate-300 pl-9 text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Cari judul, deskripsi, atau tahun..."
                        x-on:input.debounce.500ms="$refs.filterForm.submit()"
                    >
                </div>

                <select name="bulan" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua bulan</option>
                    @foreach ($months as $key => $month)
                        <option value="{{ $key }}" @selected((string) request('bulan') === (string) $key)>{{ $month }}</option>
                    @endforeach
                </select>

                <select name="tahun" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" x-on:change="$refs.filterForm.submit()">
                    <option value="">Semua tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected((string) request('tahun') === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>

                @if (request('search') || request('bulan') || request('tahun'))
                    <a href="{{ route('guru.tagihan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                        Reset
                    </a>
                @endif
            </form>

            @if ($tagihans->isEmpty())
                <x-empty-state title="Belum ada tagihan" description="Tagihan yang dibuat admin akan tampil di sini." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Judul</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Periode</th>
                                <th class="px-4 py-3">Status Ringkas</th>
                                <th class="px-4 py-3">Progress Bayar</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($tagihans as $tagihan)
                                @php
                                    $percent = $tagihan->total_siswa > 0 ? round(($tagihan->lunas_count / $tagihan->total_siswa) * 100) : 0;
                                @endphp
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $tagihan->judul }}</p>
                                        <p class="mt-1 max-w-md truncate text-xs text-slate-500">{{ $tagihan->deskripsi ?: '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $months[$tagihan->bulan] ?? $tagihan->bulan }} {{ $tagihan->tahun }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <x-badge status="lunas">{{ $tagihan->lunas_count }} lunas</x-badge>
                                            <x-badge status="pending">{{ $tagihan->pending_count }} pending</x-badge>
                                            <x-badge status="belum_bayar">{{ $tagihan->belum_bayar_count }} belum</x-badge>
                                        </div>
                                    </td>
                                    <td class="min-w-52 px-4 py-3">
                                        <div class="mb-2 flex justify-between text-xs">
                                            <span>{{ $tagihan->lunas_count }}/{{ $tagihan->total_siswa }} siswa</span>
                                            <span class="font-semibold text-primary">{{ $percent }}%</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-slate-100">
                                            <div class="h-2 rounded-full bg-primary" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('guru.tagihan.show', $tagihan) }}" class="font-semibold text-primary hover:text-blue-800">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $tagihans->links() }}</div>
            @endif
        </x-card>
    </div>
@endsection
