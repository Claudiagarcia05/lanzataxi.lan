<?php

namespace Database\Factories;

use App\Models\Viaje;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\viaje>
 */
class ViajeFactory extends Factory
{
    protected $model = Viaje::class;

    public function definition(): array
    {
        return [
            'pasajero_id' => User::factory()->state(['role' => 'pasajero']),
            'conductor_id' => null,
            'taxi_id' => null,
            'pickup_lat' => $this->faker->latitude(28.8, 29.1),
            'pickup_lng' => $this->faker->longitude(-13.8, -13.4),
            'dropoff_lat' => $this->faker->latitude(28.8, 29.1),
            'dropoff_lng' => $this->faker->longitude(-13.8, -13.4),
            'status' => 'pending',
            'distance' => null,
            'price' => null,
            'co2_saved' => null,
        ];
    }
}

