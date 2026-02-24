<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    use HasFactory;

    protected $fillable = [
        'pasajero_id', 'conductor_id', 'taxi_id',
        'pickup_lat', 'pickup_lng', 'dropoff_lat', 'dropoff_lng',
        'pickup_address', 'dropoff_address',
        'status', 'distance', 'price', 'co2_saved',
        'rating', 'comment', 'end_time',
        'pasajeros', 'luggage', 'pago_method', 'notes', 'scheduled_for',
    ];

    protected $casts = [
        'pickup_lat' => 'float',
        'pickup_lng' => 'float',
        'dropoff_lat' => 'float',
        'dropoff_lng' => 'float',
        'distance' => 'float',
        'price' => 'float',
        'co2_saved' => 'float',
        'rating' => 'integer',
        'end_time' => 'datetime',
    ];

    public function pasajero()
    {
        return $this->belongsTo(User::class, 'pasajero_id');
    }

    public function conductor()
    {
        return $this->belongsTo(Conductor::class);
    }

    public function taxi()
    {
        return $this->belongsTo(Taxi::class);
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
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

