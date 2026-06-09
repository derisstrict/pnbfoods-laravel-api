<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produk')->insert([
            [
                'nama_produk' => 'Nasi Goreng',
                'foto_produk' => null,
                'deskripsi_produk' => 'Nasi Rebus',
                'kategori_produk' => 'Makanan',
                'harga_produk' => 25000,
                'penjual_id' => 1,
                'stok' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_produk' => 'Nasi Kuning',
                'foto_produk' => null,
                'deskripsi_produk' => 'Nasi Yellow',
                'kategori_produk' => 'Makanan',
                'harga_produk' => 15000,
                'penjual_id' => 2,
                'stok' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
