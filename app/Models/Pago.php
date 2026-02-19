<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pago extends Model
{
    protected $fillable = ['viaje_id', 'amount', 'method', 'status'];

    public function viaje()
    {
        return $this->belongsTo(viaje::class);
    }
}

