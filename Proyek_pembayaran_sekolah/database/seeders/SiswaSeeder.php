<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate([
            'username' => 'siswa1',
        ], [
            'name'     => 'Siswa Contoh',
            'email'    => 'siswa@sekolah.com',
            'password' => Hash::make('siswa123'),
            'role'     => 'siswa',
        ]);

        $kelas = Kelas::firstOrCreate([
            'nama_kelas' => 'X IPA 1',
        ], [
            'wali_kelas' => 'Budi Santoso',
        ]);

        $orangTuaUser = User::firstOrCreate([
            'username' => 'ortu1',
        ], [
            'name' => 'Orang Tua Contoh',
            'email' => 'ortu@sekolah.com',
            'password' => Hash::make('ortu123'),
            'role' => 'orang_tua',
        ]);

        $orangTua = OrangTua::firstOrCreate([
            'user_id' => $orangTuaUser->id,
        ], [
            'nama' => 'Orang Tua Contoh',
            'no_hp' => '081234567890',
            'no_wa' => '081234567890',
            'alamat' => 'Alamat orang tua contoh',
        ]);

        Siswa::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'kelas_id' => $kelas->id,
            'orang_tua_id' => $orangTua->id,
            'nisn' => '1234567890',
            'nama' => 'Siswa Contoh',
            'alamat' => 'Alamat siswa contoh',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2010-01-01',
        ]);
    }
}
