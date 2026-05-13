<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate([
            'username' => 'guru1',
        ], [
            'name'     => 'Guru Contoh',
            'email'    => 'guru@sekolah.com',
            'password' => Hash::make('guru123'),
            'role'     => 'guru',
        ]);

        Guru::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'nip' => '198001012010011001',
            'nama' => 'Guru Contoh',
            'mata_pelajaran' => 'Matematika',
            'alamat' => 'Alamat guru contoh',
        ]);
    }
}
