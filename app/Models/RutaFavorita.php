<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutaFavorita extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'lat',
        'lng',
        'order'
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'order' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

