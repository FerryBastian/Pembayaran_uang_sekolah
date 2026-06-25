@php
    $isEdit = $mode === 'edit';
    $currentForm = $isEdit ? 'edit-' . $siswa->id : 'create';
    $useOld = $formId === $currentForm;
    $value = fn (string $key, mixed $fallback = '') => $useOld ? old($key) : $fallback;
@endphp

<div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
    <div class="flex items-center gap-4 p-4 sm:gap-5 sm:p-5">
        <div class="flex shrink-0 items-center justify-center rounded-lg border-4 border-orange-500 bg-white p-1">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Logo SMK GKPI 1" class="h-20 w-20 object-contain sm:h-24 sm:w-24">
        </div>

        <div class="min-w-0 flex-1 text-center font-serif text-[#073c87]">
            <p class="text-[10px] font-bold uppercase leading-tight sm:text-xs">Yayasan Pendidikan Terang Hidup</p>
            <p class="mt-1 text-xl font-black uppercase leading-none sm:text-3xl">Yayasan Pendidikan Terang Hidup</p>
            <p class="mt-1 text-sm font-black uppercase leading-tight sm:text-xl">Sekolah Menengah Kejuruan (SMK) Swasta GKPI 1</p>
            <p class="text-sm font-black uppercase leading-tight sm:text-xl">Kelompok Teknologi Rekayasa/TIK dan Kesehatan</p>
            <p class="mt-2 text-[10px] font-bold leading-tight sm:text-xs">
                Jurusan 1. Teknik Kendaraan Ringan 2. Teknik Distribusi Tenaga Listrik 3. Teknik Sepeda Motor 4. Teknik Komputer Jaringan 5. Keperawatan
            </p>
            <div class="mt-1 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-[10px] font-bold leading-tight sm:text-xs">
                <span>Jl. D. I Panjaitan No. 47 NH Pematangsiantar</span>
                <span>Email : smkgkpi@gmail.com</span>
                <span>Website : www.smkgkpisiantar.sch.id</span>
            </div>
        </div>
    </div>
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="nisn_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">NISN</label>
        <input id="nisn_{{ $currentForm }}" name="nisn" type="text" value="{{ $value('nisn', $siswa?->nisn) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('nisn') border-danger @enderror @endif" required>
        @if ($useOld) @error('nisn') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>

    <div>
        <label for="nama_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">Nama Siswa</label>
        <input id="nama_{{ $currentForm }}" name="nama" type="text" value="{{ $value('nama', $siswa?->nama) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('nama') border-danger @enderror @endif" required>
        @if ($useOld) @error('nama') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>

    <div>
        <label for="kelas_id_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">Kelas</label>
        <select id="kelas_id_{{ $currentForm }}" name="kelas_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('kelas_id') border-danger @enderror @endif" required>
            <option value="">Pilih kelas</option>
            @foreach ($kelasOptions as $kelas)
                <option value="{{ $kelas->id }}" @selected((string) $value('kelas_id', $siswa?->kelas_id) === (string) $kelas->id)>{{ $kelas->nama_kelas }}</option>
            @endforeach
        </select>
        @if ($useOld) @error('kelas_id') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>

    <div>
        <label for="orang_tua_id_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">Orang Tua</label>
        <select id="orang_tua_id_{{ $currentForm }}" name="orang_tua_id" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('orang_tua_id') border-danger @enderror @endif">
            <option value="">Belum ditautkan</option>
            @foreach ($orangTuaOptions as $orangTua)
                <option value="{{ $orangTua->id }}" @selected((string) $value('orang_tua_id', $siswa?->orang_tua_id) === (string) $orangTua->id)>{{ $orangTua->nama }}</option>
            @endforeach
        </select>
        @if ($useOld) @error('orang_tua_id') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>

    <div>
        <label for="jenis_kelamin_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">Jenis Kelamin</label>
        <select id="jenis_kelamin_{{ $currentForm }}" name="jenis_kelamin" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('jenis_kelamin') border-danger @enderror @endif" required>
            <option value="">Pilih jenis kelamin</option>
            @foreach ($genderOptions as $key => $label)
                <option value="{{ $key }}" @selected((string) $value('jenis_kelamin', $siswa?->jenis_kelamin) === (string) $key)>{{ $label }}</option>
            @endforeach
        </select>
        @if ($useOld) @error('jenis_kelamin') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>

    <div>
        <label for="tanggal_lahir_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">Tanggal Lahir</label>
        <input id="tanggal_lahir_{{ $currentForm }}" name="tanggal_lahir" type="date" value="{{ $value('tanggal_lahir', $siswa?->tanggal_lahir?->format('Y-m-d')) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('tanggal_lahir') border-danger @enderror @endif" required>
        @if ($useOld) @error('tanggal_lahir') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>

    <div>
        <label for="username_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">Username</label>
        <input id="username_{{ $currentForm }}" name="username" type="text" value="{{ $value('username', $siswa?->user?->username) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('username') border-danger @enderror @endif" required>
        @if ($useOld) @error('username') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>

    <div>
        <label for="email_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">Email</label>
        <input id="email_{{ $currentForm }}" name="email" type="email" value="{{ $value('email', $siswa?->user?->email) }}" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('email') border-danger @enderror @endif">
        @if ($useOld) @error('email') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>

    <div>
        <label for="password_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">{{ $isEdit ? 'Password Baru' : 'Password' }}</label>
        <input id="password_{{ $currentForm }}" name="password" type="password" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('password') border-danger @enderror @endif" {{ $isEdit ? '' : 'required' }} placeholder="{{ $isEdit ? 'Kosongkan jika tidak diganti' : '' }}">
        @if ($useOld) @error('password') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
    </div>
</div>

<div>
    <label for="alamat_{{ $currentForm }}" class="block text-sm font-semibold text-secondary">Alamat</label>
    <textarea id="alamat_{{ $currentForm }}" name="alamat" rows="3" class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-primary focus:ring-primary @if ($useOld) @error('alamat') border-danger @enderror @endif">{{ $value('alamat', $siswa?->alamat) }}</textarea>
    @if ($useOld) @error('alamat') <p class="mt-2 text-sm font-medium text-danger">{{ $message }}</p> @enderror @endif
</div>
