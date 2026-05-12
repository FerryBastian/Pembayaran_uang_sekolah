@extends('layouts.app')

@section('title', 'Manajemen Tagihan')
@section('header', 'Manajemen Tagihan')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Tagihan']]" />
@endsection

@section('content')
    <div class="space-y-6">
        @if (session('success')) <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert> @endif
        @if (session('error')) <x-alert type="error" title="Gagal">{{ session('error') }}</x-alert> @endif
        @if ($errors->any()) <x-alert type="error" title="Validasi gagal">Periksa kembali data tagihan.</x-alert> @endif

        <x-card title="Data Tagihan" subtitle="Kelola tagihan sekolah dan progres pembayaran siswa.">
            <x-slot:actions>
                <button type="button" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800" x-data x-on:click="$dispatch('open-modal', 'create-tagihan')">
                    Buat Tagihan
                </button>
            </x-slot:actions>

            <form method="GET" action="{{ route('admin.tagihan.index') }}" x-ref="filterForm" x-data class="mb-4 grid gap-3 lg:grid-cols-[1fr_12rem_10rem_auto]">
                <input name="search" value="{{ request('search') }}" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary" placeholder="Cari judul tagihan..." x-on:input.debounce.500ms="$refs.filterForm.submit()">
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
                    <a href="{{ route('admin.tagihan.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">Reset</a>
                @endif
            </form>

            @if ($tagihans->isEmpty())
                <x-empty-state title="Belum ada tagihan" description="Buat tagihan untuk otomatis assign ke semua siswa." />
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Judul</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Periode</th>
                                <th class="px-4 py-3">Progress Bayar</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($tagihans as $tagihan)
                                @php $percent = $tagihan->total_siswa > 0 ? round(($tagihan->sudah_bayar / $tagihan->total_siswa) * 100) : 0; @endphp
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $tagihan->judul }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $tagihan->deskripsi ?: '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $months[$tagihan->bulan] ?? $tagihan->bulan }} {{ $tagihan->tahun }}</td>
                                    <td class="min-w-52 px-4 py-3">
                                        <div class="mb-2 flex justify-between text-xs">
                                            <span>{{ $tagihan->sudah_bayar }}/{{ $tagihan->total_siswa }} siswa</span>
                                            <span class="font-semibold text-primary">{{ $percent }}%</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-primary" style="width: {{ $percent }}%"></div></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.tagihan.show', $tagihan) }}" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold text-primary hover:bg-blue-50">Detail</a>
                                            <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold text-secondary hover:bg-slate-50" x-data x-on:click="$dispatch('open-modal', 'edit-tagihan-{{ $tagihan->id }}')">Edit</button>
                                            <button type="button" class="rounded-lg border border-red-200 px-3 py-2 font-semibold text-danger hover:bg-red-50" x-data x-on:click="$dispatch('open-modal', 'delete-tagihan-{{ $tagihan->id }}')">Hapus</button>
                                        </div>
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

    <x-modal name="create-tagihan" title="Buat Tagihan" size="lg">
        <form method="POST" action="{{ route('admin.tagihan.store') }}" class="space-y-5">
            @csrf
            @include('admin.tagihan.partials.form', ['tagihan' => null, 'months' => $months, 'years' => $years, 'form' => 'create'])
            <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700" x-on:click="$dispatch('close-modal', 'create-tagihan')">Batal</button>
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white">Simpan</button>
            </div>
        </form>
    </x-modal>

    @foreach ($tagihans as $tagihan)
        <x-modal name="edit-tagihan-{{ $tagihan->id }}" title="Edit Tagihan" size="lg">
            <form method="POST" action="{{ route('admin.tagihan.update', $tagihan) }}" class="space-y-5">
                @csrf @method('PATCH')
                @include('admin.tagihan.partials.form', ['tagihan' => $tagihan, 'months' => $months, 'years' => $years, 'form' => 'edit-' . $tagihan->id])
                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700" x-on:click="$dispatch('close-modal', 'edit-tagihan-{{ $tagihan->id }}')">Batal</button>
                    <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white">Simpan</button>
                </div>
            </form>
        </x-modal>
        <x-modal name="delete-tagihan-{{ $tagihan->id }}" title="Hapus Tagihan">
            <form method="POST" action="{{ route('admin.tagihan.destroy', $tagihan) }}" class="space-y-5">
                @csrf @method('DELETE')
                <p class="text-sm text-slate-600">Hapus tagihan <span class="font-semibold text-secondary">{{ $tagihan->judul }}</span>? Tagihan yang sudah pending/lunas tidak dapat dihapus.</p>
                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700" x-on:click="$dispatch('close-modal', 'delete-tagihan-{{ $tagihan->id }}')">Batal</button>
                    <button class="rounded-xl bg-danger px-4 py-2.5 text-sm font-bold text-white">Hapus</button>
                </div>
            </form>
        </x-modal>
    @endforeach
@endsection
