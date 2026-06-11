<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class KantinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kantin')->insert([
            [
                'nama_kantin' => 'Kantin Bu Gacor',
                'foto_kantin' => null,
                'kategori' => 'Makanan',
                'created_at' => now(),
                'updated_at' => now(),
                'penjual_id' => 1
            ],
            [
                'nama_kantin' => 'Kantin Mantap Jiwa',
                'foto_kantin' => null,
                'kategori' => 'Makanan',
                'created_at' => now(),
                'updated_at' => now(),
                'penjual_id' => 2
            ],
        ]);
    }
}
