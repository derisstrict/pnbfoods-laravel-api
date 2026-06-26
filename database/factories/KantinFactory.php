<?php

namespace Database\Factories;

use App\Models\Kantin;
use App\Models\Penjual;
use Illuminate\Database\Eloquent\Factories\Factory;

class KantinFactory extends Factory
{
    protected $model = Kantin::class;

    public function definition(): array
    {
        return [
            'nama_kantin' => $this->faker->company() . ' Kantin',
            'kategori' => $this->faker->randomElement(['Makanan', 'Minuman', 'Camilan']),
            'penjual_id' => Penjual::factory(),
        ];
    }
}
