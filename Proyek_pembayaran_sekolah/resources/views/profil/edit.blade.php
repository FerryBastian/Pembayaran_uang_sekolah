@extends('layouts.app')

@section('title', 'Profil')
@section('header', 'Profil')

@section('breadcrumbs')
    <x-breadcrumb :items="[['label' => $roleLabel], ['label' => 'Profil']]" />
@endsection

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <x-alert type="success" title="Berhasil">{{ session('success') }}</x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="error" title="Validasi gagal">Periksa kembali data yang Anda masukkan.</x-alert>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1fr_0.9fr]">
            <x-card title="Informasi Profil" subtitle="Perbarui data dasar akun Anda.">
                <form method="POST" action="{{ route($routePrefix . '.profil.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-secondary">Nama</label>
                            <input
                                id="name"
                                name="name"
                                value="{{ old('name', $profile?->nama ?? $user->name) }}"
                                class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                                required
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="username" class="mb-2 block text-sm font-semibold text-secondary">Username</label>
                            <input
                                id="username"
                                value="{{ $user->username }}"
                                class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm text-slate-500 shadow-sm"
                                disabled
                            >
                        </div>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-secondary">Email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-secondary">Role</label>
                            <div class="flex h-10 items-center">
                                <x-badge :status="$user->role">{{ $roleLabel }}</x-badge>
                            </div>
                        </div>

                        @if ($user->role === 'orang_tua')
                            <div>
                                <label for="no_hp" class="mb-2 block text-sm font-semibold text-secondary">No. HP</label>
                                <input
                                    id="no_hp"
                                    name="no_hp"
                                    value="{{ old('no_hp', $profile?->no_hp) }}"
                                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                                >
                                @error('no_hp')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_wa" class="mb-2 block text-sm font-semibold text-secondary">No. WA</label>
                                <input
                                    id="no_wa"
                                    name="no_wa"
                                    value="{{ old('no_wa', $profile?->no_wa) }}"
                                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                                >
                                @error('no_wa')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if ($profile && in_array('alamat', array_keys($profile->getAttributes()), true))
                            <div class="md:col-span-2">
                                <label for="alamat" class="mb-2 block text-sm font-semibold text-secondary">Alamat</label>
                                <textarea
                                    id="alamat"
                                    name="alamat"
                                    rows="4"
                                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                                >{{ old('alamat', $profile?->alamat) }}</textarea>
                                @error('alamat')
                                    <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end border-t border-slate-200 pt-5">
                        <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </x-card>

            <x-card title="Ganti Password" subtitle="Gunakan password lama untuk mengatur password baru.">
                <form method="POST" action="{{ route($routePrefix . '.profil.password.update') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="current_password" class="mb-2 block text-sm font-semibold text-secondary">Password Lama</label>
                        <input
                            id="current_password"
                            type="password"
                            name="current_password"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                            autocomplete="current-password"
                        >
                        @error('current_password')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-secondary">Password Baru</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                            autocomplete="new-password"
                        >
                        @error('password')
                            <p class="mt-2 text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-secondary">Konfirmasi Password Baru</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary"
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="flex justify-end border-t border-slate-200 pt-5">
                        <button type="submit" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
@endsection
