<?php

namespace Database\Seeders;

use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrangTuaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate([
            'username' => 'ortu1',
        ], [
            'name'     => 'Orang Tua Contoh',
            'email'    => 'ortu@sekolah.com',
            'password' => Hash::make('ortu123'),
            'role'     => 'orang_tua',
        ]);

        OrangTua::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'nama' => 'Orang Tua Contoh',
            'no_hp' => '081234567890',
            'no_wa' => '081234567890',
            'alamat' => 'Alamat orang tua contoh',
        ]);
    }
}
