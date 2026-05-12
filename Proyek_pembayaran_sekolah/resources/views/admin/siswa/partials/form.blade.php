@php
    $isEdit = $mode === 'edit';
    $currentForm = $isEdit ? 'edit-' . $siswa->id : 'create';
    $useOld = $formId === $currentForm;
    $value = fn (string $key, mixed $fallback = '') => $useOld ? old($key) : $fallback;
@endphp

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
