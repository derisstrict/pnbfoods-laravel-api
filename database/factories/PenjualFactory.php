<?php

namespace Database\Factories;

use App\Models\Penjual;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class PenjualFactory extends Factory
{
    protected $model = Penjual::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'nama' => $this->faker->name(),
            'password' => Hash::make('password'),
        ];
    }
}
