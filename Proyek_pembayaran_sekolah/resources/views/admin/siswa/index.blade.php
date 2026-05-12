@extends('layouts.app')

@section('title', 'Manajemen Siswa')
@section('header', 'Manajemen Siswa')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Siswa']]" />
@endsection

@php
    $formId = old('_form');
    $genderOptions = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
@endphp

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" title="Gagal">{{ session('error') }}</x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="error" title="Validasi gagal">Periksa kembali data siswa yang Anda masukkan.</x-alert>
        @endif

        <x-card title="Data Siswa" subtitle="Kelola data siswa, kelas, orang tua, dan akun login siswa.">
            <x-slot:actions>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800"
                    x-data
                    x-on:click="$dispatch('open-modal', 'create-siswa')"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Tambah Siswa
                </button>
            </x-slot:actions>

            <form method="GET" action="{{ route('admin.siswa.index') }}" x-ref="filterForm" x-data class="mb-4 grid gap-3 lg:grid-cols-[1fr_16rem_auto]">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border-slate-300 pl-9 text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Cari NISN, nama, kelas, orang tua, username..."
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
                    <a href="{{ route('admin.siswa.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-primary hover:bg-blue-50">
                        Reset
                    </a>
                @endif
            </form>

            @if ($siswas->isEmpty())
                <x-empty-state title="Belum ada siswa" description="Tambahkan siswa agar data tagihan dapat dibuat per peserta didik.">
                    <x-slot:action>
                        <button type="button" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800" x-data x-on:click="$dispatch('open-modal', 'create-siswa')">
                            Tambah Siswa
                        </button>
                    </x-slot:action>
                </x-empty-state>
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
                                        <p class="mt-1 text-xs text-slate-500">{{ $siswa->user?->username ?: '-' }}{{ $siswa->user?->email ? ' | ' . $siswa->user->email : '' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-badge status="info">{{ $siswa->kelas?->nama_kelas ?? '-' }}</x-badge></td>
                                    <td class="px-4 py-3 text-slate-600">{{ $siswa->orangTua?->nama ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-secondary hover:border-primary hover:bg-blue-50 hover:text-primary" x-data x-on:click="$dispatch('open-modal', 'edit-siswa-{{ $siswa->id }}')">Edit</button>
                                            <button type="button" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-danger hover:bg-red-50" x-data x-on:click="$dispatch('open-modal', 'delete-siswa-{{ $siswa->id }}')">Hapus</button>
                                        </div>
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

    <x-modal name="create-siswa" title="Tambah Siswa" size="xl">
        <form method="POST" action="{{ route('admin.siswa.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="_form" value="create">
            @include('admin.siswa.partials.form', [
                'mode' => 'create',
                'siswa' => null,
                'kelasOptions' => $kelasOptions,
                'orangTuaOptions' => $orangTuaOptions,
                'genderOptions' => $genderOptions,
                'formId' => $formId,
            ])
            <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'create-siswa')">Batal</button>
                <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Simpan</button>
            </div>
        </form>
    </x-modal>

    @foreach ($siswas as $siswa)
        <x-modal name="edit-siswa-{{ $siswa->id }}" title="Edit Siswa" size="xl">
            <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_form" value="edit-{{ $siswa->id }}">
                @include('admin.siswa.partials.form', [
                    'mode' => 'edit',
                    'siswa' => $siswa,
                    'kelasOptions' => $kelasOptions,
                    'orangTuaOptions' => $orangTuaOptions,
                    'genderOptions' => $genderOptions,
                    'formId' => $formId,
                ])
                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'edit-siswa-{{ $siswa->id }}')">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="delete-siswa-{{ $siswa->id }}" title="Hapus Siswa">
            <form method="POST" action="{{ route('admin.siswa.destroy', $siswa) }}" class="space-y-5">
                @csrf
                @method('DELETE')
                <p class="text-sm leading-6 text-slate-600">
                    Anda yakin ingin menghapus siswa <span class="font-semibold text-secondary">{{ $siswa->nama }}</span>?
                    Akun login siswa ini juga akan terhapus.
                </p>
                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'delete-siswa-{{ $siswa->id }}')">Batal</button>
                    <button type="submit" class="rounded-xl bg-danger px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </x-modal>
    @endforeach

    @if ($errors->any() && $formId === 'create')
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-siswa' })), 50);
            });
        </script>
    @endif

    @if ($errors->any() && str_starts_with((string) $formId, 'edit-'))
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: '{{ $formId ? str_replace('edit-', 'edit-siswa-', $formId) : '' }}' })), 50);
            });
        </script>
    @endif
@endsection
