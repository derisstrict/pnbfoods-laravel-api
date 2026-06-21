<?php

namespace Database\Factories;

use App\Models\Orderan;
use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderanFactory extends Factory
{
    protected $model = Orderan::class;

    public function definition(): array
    {
        return [
            'pelanggan_id' => Pelanggan::factory(),
            'status_orderan' => 'menunggu_pembayaran',
            'total_harga' => $this->faker->numberBetween(10000, 200000),
            'tanggal_orderan' => now(),
        ];
    }
}
