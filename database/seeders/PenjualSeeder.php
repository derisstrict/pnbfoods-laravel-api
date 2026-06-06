<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenjualSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('penjual')->insert([
            [
                'email' => 'b@gmail.com',
                'nama' => 'b',
                'role' => 'penjual', 
                'password' => Hash::make('b'),
                'foto_profile' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}