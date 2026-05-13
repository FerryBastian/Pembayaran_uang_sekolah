<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [
            ['nama_kelas' => 'X IPA 1',  'wali_kelas' => 'Budi Santoso'],
            ['nama_kelas' => 'X IPA 2',  'wali_kelas' => 'Siti Rahayu'],
            ['nama_kelas' => 'X IPS 1',  'wali_kelas' => 'Ahmad Fauzi'],
            ['nama_kelas' => 'XI IPA 1', 'wali_kelas' => 'Dewi Lestari'],
            ['nama_kelas' => 'XI IPA 2', 'wali_kelas' => 'Rudi Hartono'],
            ['nama_kelas' => 'XII IPA 1','wali_kelas' => 'Eni Suryani'],
        ];

        foreach ($kelas as $k) {
            Kelas::updateOrCreate(
                ['nama_kelas' => $k['nama_kelas']],
                ['wali_kelas' => $k['wali_kelas']]
            );
        }
    }
}
