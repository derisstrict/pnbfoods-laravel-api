<?php

namespace Database\Factories;

use App\Models\DetailOrderan;
use App\Models\Orderan;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailOrderanFactory extends Factory
{
    protected $model = DetailOrderan::class;

    public function definition(): array
    {
        return [
            'orderan_id' => Orderan::factory(),
            'produk_id' => Produk::factory(),
            'jumlah' => $this->faker->numberBetween(1, 5),
        ];
    }
}
