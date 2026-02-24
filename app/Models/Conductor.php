<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'license_number', 'rating', 'is_active'];

    protected $casts = [
        'rating' => 'float',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taxi()
    {
        return $this->hasOne(Taxi::class);
    }

    public function viajes()
    {
        return $this->hasMany(Viaje::class);
    }

    public function ubicacions()
    {
        return $this->hasMany(ubicacion::class);
    }
}

