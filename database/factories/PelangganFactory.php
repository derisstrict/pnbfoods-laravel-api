<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class PelangganFactory extends Factory
{
    protected $model = Pelanggan::class;

    public function definition(): array
    {
        return [
            'nim' => $this->faker->unique()->numerify('##########'),
            'nama' => $this->faker->name(),
            'password' => Hash::make('password'),
        ];
    }
}
