<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'driver']),
            'license_number' => $this->faker->unique()->bothify('LIC-#####'),
            'rating' => 5.0,
            'is_active' => false,
        ];
    }
}
