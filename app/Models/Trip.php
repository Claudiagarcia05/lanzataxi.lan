<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'passenger_id', 'driver_id', 'taxi_id',
        'pickup_lat', 'pickup_lng', 'dropoff_lat', 'dropoff_lng',
        'status', 'distance', 'price', 'co2_saved',
    ];

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function taxi()
    {
        return $this->belongsTo(Taxi::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Metodo para calcular CO2 ahorrado
    public function calculateCO2Saved()
    {
        if ($this->distance) {
            $co2_coche = $this->distance * 0.120; // 120g/km
            $co2_taxi = $this->distance * 0.080;  // 80g/km por pasajero
            return round($co2_coche - $co2_taxi, 2);
        }

        return 0;
    }
}
