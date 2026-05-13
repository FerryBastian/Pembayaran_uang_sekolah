<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Siswa Contoh',
            'username' => 'siswa1',
            'email'    => 'siswa@sekolah.com',
            'password' => Hash::make('siswa123'),
            'role'     => 'siswa',
        ]);
    }
}
