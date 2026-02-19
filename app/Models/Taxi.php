<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxi extends Model
{
    use HasFactory;

    protected $fillable = ['conductor_id', 'plate', 'model', 'capacity', 'color', 'status'];

    public function conductor()
    {
        return $this->belongsTo(conductor::class);
    }

    public function viajes()
    {
        return $this->hasMany(viaje::class);
    }
}

