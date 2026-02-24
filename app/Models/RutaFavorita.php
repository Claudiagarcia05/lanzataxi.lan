<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutaFavorita extends Model
{
    use HasFactory;

    protected $table = 'rutas_favoritas';

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

