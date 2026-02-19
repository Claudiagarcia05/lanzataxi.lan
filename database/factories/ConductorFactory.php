<?php

namespace Database\Factories;

use App\Models\conductor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\conductor>
 */
class ConductorFactory extends Factory
{
    protected $model = conductor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'conductor']),
            'license_number' => $this->faker->unique()->bothify('LIC-#####'),
            'rating' => 5.0,
            'is_active' => false,
        ];
    }
}

