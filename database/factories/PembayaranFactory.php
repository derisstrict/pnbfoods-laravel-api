<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Orderan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PembayaranFactory extends Factory
{
    protected $model = Pembayaran::class;

    public function definition(): array
    {
        return [
            'orderan_id' => Orderan::factory(),
            'metode_pembayaran' => 'QRIS',
            'total_pembayaran' => $this->faker->numberBetween(10000, 200000),
            'status_pembayaran' => 'menunggu_pembayaran',
        ];
    }
}
