@extends('layouts.app')

@section('title', 'Manajemen Orang Tua')
@section('header', 'Manajemen Orang Tua')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Orang Tua']]" />
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
                Periksa kembali data orang tua yang Anda masukkan.
            </x-alert>
        @endif

        <x-card title="Data Orang Tua" subtitle="Kelola akun orang tua dan data kontak wali siswa.">
            <x-slot:actions>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800"
                    x-data
                    x-on:click="$dispatch('open-modal', 'create-orang-tua')"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Tambah Orang Tua
                </button>
            </x-slot:actions>

            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('admin.orang-tua.index') }}" x-ref="searchForm" x-data class="relative w-full sm:max-w-sm">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border-slate-300 pl-9 text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Cari nama, HP, WA, username..."
                        x-on:input.debounce.500ms="$refs.searchForm.submit()"
                    >
                </form>

                @if (request('search'))
                    <a href="{{ route('admin.orang-tua.index') }}" class="text-sm font-semibold text-primary hover:text-blue-800">
                        Reset pencarian
                    </a>
                @endif
            </div>

            @if ($orangTuas->isEmpty())
                <x-empty-state
                    title="Belum ada orang tua"
                    description="Tambahkan akun orang tua agar wali siswa dapat mengakses tagihan anak."
                >
                    <x-slot:action>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800"
                            x-data
                            x-on:click="$dispatch('open-modal', 'create-orang-tua')"
                        >
                            Tambah Orang Tua
                        </button>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">No HP</th>
                                <th class="px-4 py-3">No WA</th>
                                <th class="px-4 py-3">Username</th>
                                <th class="px-4 py-3">Jumlah Anak</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($orangTuas as $orangTua)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $orangTua->nama }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $orangTua->user?->email ?: '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $orangTua->no_hp ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $orangTua->no_wa ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-badge status="orang_tua">{{ $orangTua->user?->username ?: '-' }}</x-badge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-badge status="info">{{ number_format($orangTua->siswas_count, 0, ',', '.') }} anak</x-badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-secondary hover:border-primary hover:bg-blue-50 hover:text-primary"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'edit-orang-tua-{{ $orangTua->id }}')"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-danger hover:bg-red-50"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'delete-orang-tua-{{ $orangTua->id }}')"
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
                    {{ $orangTuas->links() }}
                </div>
            @endif
        </x-card>
    </div>

    <x-modal name="create-orang-tua" title="Tambah Orang Tua" size="lg">
        <form method="POST" action="{{ route('admin.orang-tua.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="nama" class="block text-sm font-semibold text-secondary">Nama</label>
                    <input id="nama" name="nama" type="text" value="{{ old('_form') === 'create' ? old('nama') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('nama') border-danger @enderror @endif" required>
                    @if (old('_form') === 'create') @error('nama') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div>
                    <label for="username" class="block text-sm font-semibold text-secondary">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('_form') === 'create' ? old('username') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('username') border-danger @enderror @endif" required>
                    @if (old('_form') === 'create') @error('username') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-semibold text-secondary">No HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('_form') === 'create' ? old('no_hp') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('no_hp') border-danger @enderror @endif" required>
                    @if (old('_form') === 'create') @error('no_hp') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div>
                    <label for="no_wa" class="block text-sm font-semibold text-secondary">No WA</label>
                    <input id="no_wa" name="no_wa" type="text" value="{{ old('_form') === 'create' ? old('no_wa') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('no_wa') border-danger @enderror @endif" placeholder="Kosongkan jika sama dengan No HP">
                    @if (old('_form') === 'create') @error('no_wa') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-secondary">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('_form') === 'create' ? old('email') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('email') border-danger @enderror @endif">
                    @if (old('_form') === 'create') @error('email') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-secondary">Password</label>
                    <input id="password" name="password" type="password" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('password') border-danger @enderror @endif" required>
                    @if (old('_form') === 'create') @error('password') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-sm font-semibold text-secondary">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('alamat') border-danger @enderror @endif">{{ old('_form') === 'create' ? old('alamat') : '' }}</textarea>
                @if (old('_form') === 'create') @error('alamat') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'create-orang-tua')">Batal</button>
                <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Simpan</button>
            </div>
        </form>
    </x-modal>

    @foreach ($orangTuas as $orangTua)
        <x-modal name="edit-orang-tua-{{ $orangTua->id }}" title="Edit Orang Tua" size="lg">
            <form method="POST" action="{{ route('admin.orang-tua.update', $orangTua) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_form" value="edit-{{ $orangTua->id }}">

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nama_{{ $orangTua->id }}" class="block text-sm font-semibold text-secondary">Nama</label>
                        <input id="nama_{{ $orangTua->id }}" name="nama" type="text" value="{{ old('_form') === 'edit-' . $orangTua->id ? old('nama') : $orangTua->nama }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $orangTua->id) @error('nama') border-danger @enderror @endif" required>
                        @if (old('_form') === 'edit-' . $orangTua->id) @error('nama') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="username_{{ $orangTua->id }}" class="block text-sm font-semibold text-secondary">Username</label>
                        <input id="username_{{ $orangTua->id }}" name="username" type="text" value="{{ old('_form') === 'edit-' . $orangTua->id ? old('username') : $orangTua->user?->username }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $orangTua->id) @error('username') border-danger @enderror @endif" required>
                        @if (old('_form') === 'edit-' . $orangTua->id) @error('username') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="no_hp_{{ $orangTua->id }}" class="block text-sm font-semibold text-secondary">No HP</label>
                        <input id="no_hp_{{ $orangTua->id }}" name="no_hp" type="text" value="{{ old('_form') === 'edit-' . $orangTua->id ? old('no_hp') : $orangTua->no_hp }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $orangTua->id) @error('no_hp') border-danger @enderror @endif" required>
                        @if (old('_form') === 'edit-' . $orangTua->id) @error('no_hp') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="no_wa_{{ $orangTua->id }}" class="block text-sm font-semibold text-secondary">No WA</label>
                        <input id="no_wa_{{ $orangTua->id }}" name="no_wa" type="text" value="{{ old('_form') === 'edit-' . $orangTua->id ? old('no_wa') : $orangTua->no_wa }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $orangTua->id) @error('no_wa') border-danger @enderror @endif">
                        @if (old('_form') === 'edit-' . $orangTua->id) @error('no_wa') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="email_{{ $orangTua->id }}" class="block text-sm font-semibold text-secondary">Email</label>
                        <input id="email_{{ $orangTua->id }}" name="email" type="email" value="{{ old('_form') === 'edit-' . $orangTua->id ? old('email') : $orangTua->user?->email }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $orangTua->id) @error('email') border-danger @enderror @endif">
                        @if (old('_form') === 'edit-' . $orangTua->id) @error('email') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="password_{{ $orangTua->id }}" class="block text-sm font-semibold text-secondary">Password Baru</label>
                        <input id="password_{{ $orangTua->id }}" name="password" type="password" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $orangTua->id) @error('password') border-danger @enderror @endif" placeholder="Kosongkan jika tidak diganti">
                        @if (old('_form') === 'edit-' . $orangTua->id) @error('password') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>
                </div>

                <div>
                    <label for="alamat_{{ $orangTua->id }}" class="block text-sm font-semibold text-secondary">Alamat</label>
                    <textarea id="alamat_{{ $orangTua->id }}" name="alamat" rows="3" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $orangTua->id) @error('alamat') border-danger @enderror @endif">{{ old('_form') === 'edit-' . $orangTua->id ? old('alamat') : $orangTua->alamat }}</textarea>
                    @if (old('_form') === 'edit-' . $orangTua->id) @error('alamat') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'edit-orang-tua-{{ $orangTua->id }}')">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="delete-orang-tua-{{ $orangTua->id }}" title="Hapus Orang Tua">
            <form method="POST" action="{{ route('admin.orang-tua.destroy', $orangTua) }}" class="space-y-5">
                @csrf
                @method('DELETE')

                <p class="text-sm leading-6 text-slate-600">
                    Anda yakin ingin menghapus orang tua <span class="font-semibold text-secondary">{{ $orangTua->nama }}</span>?
                    Akun login orang tua ini juga akan terhapus.
                </p>

                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'delete-orang-tua-{{ $orangTua->id }}')">Batal</button>
                    <button type="submit" class="rounded-xl bg-danger px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </x-modal>
    @endforeach

    @if ($errors->any() && old('_form') === 'create')
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-orang-tua' })), 50);
            });
        </script>
    @endif

    @if ($errors->any() && str_starts_with((string) old('_form'), 'edit-'))
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: '{{ old('_form') ? str_replace('edit-', 'edit-orang-tua-', old('_form')) : '' }}' })), 50);
            });
        </script>
    @endif
@endsection
