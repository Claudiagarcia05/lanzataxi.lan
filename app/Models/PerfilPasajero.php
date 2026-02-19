<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilPasajero extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'phone_alternative',
        'preferences',
        'total_viajes',
        'total_spent'
    ];

    protected $casts = [
        'preferences' => 'array',
        'total_viajes' => 'integer',
        'total_spent' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

