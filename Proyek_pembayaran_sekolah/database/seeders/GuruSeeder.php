<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Guru Contoh',
            'username' => 'guru1',
            'email'    => 'guru@sekolah.com',
            'password' => Hash::make('guru123'),
            'role'     => 'guru',
        ]);
    }
}
