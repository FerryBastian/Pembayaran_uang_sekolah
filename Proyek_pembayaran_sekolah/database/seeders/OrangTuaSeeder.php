<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrangTuaSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Orang Tua Contoh',
            'username' => 'ortu1',
            'email'    => 'ortu@sekolah.com',
            'password' => Hash::make('ortu123'),
            'role'     => 'orang_tua',
        ]);
    }
}
