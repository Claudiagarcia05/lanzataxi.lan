<?php

namespace Database\Factories;

use App\Models\RutaFavorita;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RutaFavoritaFactory extends Factory
{
    protected $model = RutaFavorita::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'address' => $this->faker->address(),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'order' => $this->faker->randomDigit(),
        ];
    }
}
