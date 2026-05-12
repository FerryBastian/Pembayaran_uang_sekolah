@extends('layouts.app')

@section('title', 'Manajemen Kelas')
@section('header', 'Manajemen Kelas')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Kelas']]" />
@endsection

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <x-alert type="success" title="Berhasil">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" title="Gagal">
                {{ session('error') }}
            </x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="error" title="Validasi gagal">
                Periksa kembali data yang Anda masukkan.
            </x-alert>
        @endif

        <x-card title="Data Kelas" subtitle="Kelola kelas dan wali kelas untuk data siswa.">
            <x-slot:actions>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800"
                    x-data
                    x-on:click="$dispatch('open-modal', 'create-kelas')"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Tambah Kelas
                </button>
            </x-slot:actions>

            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('admin.kelas.index') }}" x-ref="searchForm" x-data class="relative w-full sm:max-w-sm">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border-slate-300 pl-9 text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Cari nama kelas atau wali kelas..."
                        x-on:input.debounce.500ms="$refs.searchForm.submit()"
                    >
                </form>

                @if (request('search'))
                    <a href="{{ route('admin.kelas.index') }}" class="text-sm font-semibold text-primary hover:text-blue-800">
                        Reset pencarian
                    </a>
                @endif
            </div>

            @if ($kelas->isEmpty())
                <x-empty-state
                    title="Belum ada kelas"
                    description="Tambahkan kelas agar data siswa bisa dikelompokkan dengan benar."
                >
                    <x-slot:action>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800"
                            x-data
                            x-on:click="$dispatch('open-modal', 'create-kelas')"
                        >
                            Tambah Kelas
                        </button>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Nama Kelas</th>
                                <th class="px-4 py-3">Wali Kelas</th>
                                <th class="px-4 py-3">Jumlah Siswa</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($kelas as $item)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $item->nama_kelas }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $item->wali_kelas ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-badge status="info">{{ number_format($item->siswas_count, 0, ',', '.') }} siswa</x-badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-secondary hover:border-primary hover:bg-blue-50 hover:text-primary"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'edit-kelas-{{ $item->id }}')"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-danger hover:bg-red-50"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'delete-kelas-{{ $item->id }}')"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $kelas->links() }}
                </div>
            @endif
        </x-card>
    </div>

    <x-modal name="create-kelas" title="Tambah Kelas">
        <form method="POST" action="{{ route('admin.kelas.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div>
                <label for="nama_kelas" class="block text-sm font-semibold text-secondary">Nama Kelas</label>
                <input
                    id="nama_kelas"
                    name="nama_kelas"
                    type="text"
                    value="{{ old('_form') === 'create' ? old('nama_kelas') : '' }}"
                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @error('nama_kelas') border-danger @enderror"
                    placeholder="Contoh: VII A"
                    required
                >
                @if (old('_form') === 'create')
                    @error('nama_kelas')
                        <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div>
                <label for="wali_kelas" class="block text-sm font-semibold text-secondary">Wali Kelas</label>
                <input
                    id="wali_kelas"
                    name="wali_kelas"
                    type="text"
                    value="{{ old('_form') === 'create' ? old('wali_kelas') : '' }}"
                    class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @error('wali_kelas') border-danger @enderror"
                    placeholder="Nama wali kelas"
                >
                @if (old('_form') === 'create')
                    @error('wali_kelas')
                        <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'create-kelas')">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                    Simpan
                </button>
            </div>
        </form>
    </x-modal>

    @foreach ($kelas as $item)
        <x-modal name="edit-kelas-{{ $item->id }}" title="Edit Kelas">
            <form method="POST" action="{{ route('admin.kelas.update', $item) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_form" value="edit-{{ $item->id }}">

                <div>
                    <label for="nama_kelas_{{ $item->id }}" class="block text-sm font-semibold text-secondary">Nama Kelas</label>
                    <input
                        id="nama_kelas_{{ $item->id }}"
                        name="nama_kelas"
                        type="text"
                        value="{{ old('_form') === 'edit-' . $item->id ? old('nama_kelas') : $item->nama_kelas }}"
                        class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $item->id) @error('nama_kelas') border-danger @enderror @endif"
                        required
                    >
                    @if (old('_form') === 'edit-' . $item->id)
                        @error('nama_kelas')
                            <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div>
                    <label for="wali_kelas_{{ $item->id }}" class="block text-sm font-semibold text-secondary">Wali Kelas</label>
                    <input
                        id="wali_kelas_{{ $item->id }}"
                        name="wali_kelas"
                        type="text"
                        value="{{ old('_form') === 'edit-' . $item->id ? old('wali_kelas') : $item->wali_kelas }}"
                        class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $item->id) @error('wali_kelas') border-danger @enderror @endif"
                    >
                    @if (old('_form') === 'edit-' . $item->id)
                        @error('wali_kelas')
                            <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'edit-kelas-{{ $item->id }}')">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-modal>

        <x-modal name="delete-kelas-{{ $item->id }}" title="Hapus Kelas">
            <form method="POST" action="{{ route('admin.kelas.destroy', $item) }}" class="space-y-5">
                @csrf
                @method('DELETE')

                <p class="text-sm leading-6 text-slate-600">
                    Anda yakin ingin menghapus kelas <span class="font-semibold text-secondary">{{ $item->nama_kelas }}</span>?
                    Kelas yang masih memiliki siswa tidak dapat dihapus.
                </p>

                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'delete-kelas-{{ $item->id }}')">
                        Batal
                    </button>
                    <button type="submit" class="rounded-xl bg-danger px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                        Hapus
                    </button>
                </div>
            </form>
        </x-modal>
    @endforeach

    @if ($errors->any() && old('_form') === 'create')
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-kelas' })), 50);
            });
        </script>
    @endif

    @if ($errors->any() && str_starts_with((string) old('_form'), 'edit-'))
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: '{{ old('_form') ? str_replace('edit-', 'edit-kelas-', old('_form')) : '' }}' })), 50);
            });
        </script>
    @endif
@endsection
