<?php

namespace Database\Factories;

use App\Models\Produk;
use App\Models\Penjual;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    public function definition(): array
    {
        return [
            'penjual_id' => Penjual::factory(),
            'nama_produk' => $this->faker->word() . ' ' . $this->faker->word(),
            'kategori_produk' => $this->faker->randomElement(['Makanan', 'Minuman', 'Camilan']),
            'harga_produk' => $this->faker->numberBetween(5000, 50000),
            'stok' => $this->faker->numberBetween(0, 100),
        ];
    }
}
