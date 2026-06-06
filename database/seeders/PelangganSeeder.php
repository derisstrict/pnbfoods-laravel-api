<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pelanggan')->insert([
            [
                'nim' => '2415354001',
                'nama' => 'a',
                'role' => 'pelanggan',
                'password' => Hash::make('a'),
                'foto_profile' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}