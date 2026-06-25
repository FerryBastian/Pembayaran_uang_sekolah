<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'username' => 'admin',
        ], [
            'name'     => 'Heppy Manalu',
            'email'    => 'admin@sekolah.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);
    }
}
