<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Taxi;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TripBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_book_a_trip(): void
    {
        $user = User::factory()->create(['role' => 'passenger']);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/trips', [
            'pickup_lat' => 28.963,
            'pickup_lng' => -13.550,
            'dropoff_lat' => 28.978,
            'dropoff_lng' => -13.561,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'status', 'price']);
    }

    public function test_trip_can_be_accepted_by_driver(): void
    {
        $driverUser = User::factory()->create(['role' => 'driver']);
        $driver = Driver::factory()->create(['user_id' => $driverUser->id]);
        Taxi::factory()->create(['driver_id' => $driver->id]);

        $trip = Trip::factory()->create(['status' => 'pending']);

        Sanctum::actingAs($driverUser);

        $response = $this->patchJson("/api/trips/{$trip->id}/accept");

        $response->assertStatus(200);
        $this->assertEquals('accepted', $trip->fresh()->status);
    }
}
