@extends('layouts.app')

@section('title', 'Manajemen Guru')
@section('header', 'Manajemen Guru')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => 'Admin'], ['label' => 'Guru']]" />
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
                Periksa kembali data guru yang Anda masukkan.
            </x-alert>
        @endif

        <x-card title="Data Guru" subtitle="Kelola akun guru, identitas, dan mata pelajaran.">
            <x-slot:actions>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800"
                    x-data
                    x-on:click="$dispatch('open-modal', 'create-guru')"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Tambah Guru
                </button>
            </x-slot:actions>

            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('admin.guru.index') }}" x-ref="searchForm" x-data class="relative w-full sm:max-w-sm">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m21 21-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        class="w-full rounded-xl border-slate-300 pl-9 text-sm shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Cari NIP, nama, mapel, username..."
                        x-on:input.debounce.500ms="$refs.searchForm.submit()"
                    >
                </form>

                @if (request('search'))
                    <a href="{{ route('admin.guru.index') }}" class="text-sm font-semibold text-primary hover:text-blue-800">
                        Reset pencarian
                    </a>
                @endif
            </div>

            @if ($gurus->isEmpty())
                <x-empty-state
                    title="Belum ada guru"
                    description="Tambahkan guru untuk membuat akun dan data pengajar."
                >
                    <x-slot:action>
                        <button
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800"
                            x-data
                            x-on:click="$dispatch('open-modal', 'create-guru')"
                        >
                            Tambah Guru
                        </button>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                            <tr>
                                <th class="px-4 py-3">NIP</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Mata Pelajaran</th>
                                <th class="px-4 py-3">Username</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($gurus as $guru)
                                <tr class="hover:bg-blue-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-secondary">{{ $guru->nip ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-secondary">{{ $guru->nama }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $guru->user?->email ?: '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $guru->mata_pelajaran ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <x-badge status="guru">{{ $guru->user?->username ?: '-' }}</x-badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-secondary hover:border-primary hover:bg-blue-50 hover:text-primary"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'edit-guru-{{ $guru->id }}')"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-danger hover:bg-red-50"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'delete-guru-{{ $guru->id }}')"
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
                    {{ $gurus->links() }}
                </div>
            @endif
        </x-card>
    </div>

    <x-modal name="create-guru" title="Tambah Guru" size="lg">
        <form method="POST" action="{{ route('admin.guru.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="nip" class="block text-sm font-semibold text-secondary">NIP</label>
                    <input id="nip" name="nip" type="text" value="{{ old('_form') === 'create' ? old('nip') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('nip') border-danger @enderror @endif" required>
                    @if (old('_form') === 'create') @error('nip') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div>
                    <label for="nama" class="block text-sm font-semibold text-secondary">Nama Guru</label>
                    <input id="nama" name="nama" type="text" value="{{ old('_form') === 'create' ? old('nama') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('nama') border-danger @enderror @endif" required>
                    @if (old('_form') === 'create') @error('nama') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div>
                    <label for="mata_pelajaran" class="block text-sm font-semibold text-secondary">Mata Pelajaran</label>
                    <input id="mata_pelajaran" name="mata_pelajaran" type="text" value="{{ old('_form') === 'create' ? old('mata_pelajaran') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('mata_pelajaran') border-danger @enderror @endif">
                    @if (old('_form') === 'create') @error('mata_pelajaran') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div>
                    <label for="username" class="block text-sm font-semibold text-secondary">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('_form') === 'create' ? old('username') : '' }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'create') @error('username') border-danger @enderror @endif" required>
                    @if (old('_form') === 'create') @error('username') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
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
                <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'create-guru')">Batal</button>
                <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Simpan</button>
            </div>
        </form>
    </x-modal>

    @foreach ($gurus as $guru)
        <x-modal name="edit-guru-{{ $guru->id }}" title="Edit Guru" size="lg">
            <form method="POST" action="{{ route('admin.guru.update', $guru) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_form" value="edit-{{ $guru->id }}">

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nip_{{ $guru->id }}" class="block text-sm font-semibold text-secondary">NIP</label>
                        <input id="nip_{{ $guru->id }}" name="nip" type="text" value="{{ old('_form') === 'edit-' . $guru->id ? old('nip') : $guru->nip }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $guru->id) @error('nip') border-danger @enderror @endif" required>
                        @if (old('_form') === 'edit-' . $guru->id) @error('nip') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="nama_{{ $guru->id }}" class="block text-sm font-semibold text-secondary">Nama Guru</label>
                        <input id="nama_{{ $guru->id }}" name="nama" type="text" value="{{ old('_form') === 'edit-' . $guru->id ? old('nama') : $guru->nama }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $guru->id) @error('nama') border-danger @enderror @endif" required>
                        @if (old('_form') === 'edit-' . $guru->id) @error('nama') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="mata_pelajaran_{{ $guru->id }}" class="block text-sm font-semibold text-secondary">Mata Pelajaran</label>
                        <input id="mata_pelajaran_{{ $guru->id }}" name="mata_pelajaran" type="text" value="{{ old('_form') === 'edit-' . $guru->id ? old('mata_pelajaran') : $guru->mata_pelajaran }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $guru->id) @error('mata_pelajaran') border-danger @enderror @endif">
                        @if (old('_form') === 'edit-' . $guru->id) @error('mata_pelajaran') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="username_{{ $guru->id }}" class="block text-sm font-semibold text-secondary">Username</label>
                        <input id="username_{{ $guru->id }}" name="username" type="text" value="{{ old('_form') === 'edit-' . $guru->id ? old('username') : $guru->user?->username }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $guru->id) @error('username') border-danger @enderror @endif" required>
                        @if (old('_form') === 'edit-' . $guru->id) @error('username') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="email_{{ $guru->id }}" class="block text-sm font-semibold text-secondary">Email</label>
                        <input id="email_{{ $guru->id }}" name="email" type="email" value="{{ old('_form') === 'edit-' . $guru->id ? old('email') : $guru->user?->email }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $guru->id) @error('email') border-danger @enderror @endif">
                        @if (old('_form') === 'edit-' . $guru->id) @error('email') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>

                    <div>
                        <label for="password_{{ $guru->id }}" class="block text-sm font-semibold text-secondary">Password Baru</label>
                        <input id="password_{{ $guru->id }}" name="password" type="password" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $guru->id) @error('password') border-danger @enderror @endif" placeholder="Kosongkan jika tidak diganti">
                        @if (old('_form') === 'edit-' . $guru->id) @error('password') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                    </div>
                </div>

                <div>
                    <label for="alamat_{{ $guru->id }}" class="block text-sm font-semibold text-secondary">Alamat</label>
                    <textarea id="alamat_{{ $guru->id }}" name="alamat" rows="3" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if (old('_form') === 'edit-' . $guru->id) @error('alamat') border-danger @enderror @endif">{{ old('_form') === 'edit-' . $guru->id ? old('alamat') : $guru->alamat }}</textarea>
                    @if (old('_form') === 'edit-' . $guru->id) @error('alamat') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'edit-guru-{{ $guru->id }}')">Batal</button>
                    <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Simpan Perubahan</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="delete-guru-{{ $guru->id }}" title="Hapus Guru">
            <form method="POST" action="{{ route('admin.guru.destroy', $guru) }}" class="space-y-5">
                @csrf
                @method('DELETE')

                <p class="text-sm leading-6 text-slate-600">
                    Anda yakin ingin menghapus guru <span class="font-semibold text-secondary">{{ $guru->nama }}</span>?
                    Akun login guru ini juga akan terhapus.
                </p>

                <div class="flex justify-end gap-2 border-t border-slate-200 pt-5">
                    <button type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50" x-on:click="$dispatch('close-modal', 'delete-guru-{{ $guru->id }}')">Batal</button>
                    <button type="submit" class="rounded-xl bg-danger px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                </div>
            </form>
        </x-modal>
    @endforeach

    @if ($errors->any() && old('_form') === 'create')
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: 'create-guru' })), 50);
            });
        </script>
    @endif

    @if ($errors->any() && str_starts_with((string) old('_form'), 'edit-'))
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-modal', { detail: '{{ old('_form') ? str_replace('edit-', 'edit-guru-', old('_form')) : '' }}' })), 50);
            });
        </script>
    @endif
@endsection
