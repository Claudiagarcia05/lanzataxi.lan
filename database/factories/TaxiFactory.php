<?php

namespace Database\Factories;

use App\Models\conductor;
use App\Models\Taxi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Taxi>
 */
class TaxiFactory extends Factory
{
    protected $model = Taxi::class;

    public function definition(): array
    {
        return [
            'conductor_id' => conductor::factory(),
            'plate' => $this->faker->unique()->bothify('####-???'),
            'model' => $this->faker->word(),
            'capacity' => 4,
            'color' => $this->faker->safeColorName(),
            'status' => 'available',
        ];
    }
}

