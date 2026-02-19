<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ubicacion extends Model
{
    protected $fillable = ['conductor_id', 'lat', 'lng'];

    public function conductor()
    {
        return $this->belongsTo(conductor::class);
    }
}

