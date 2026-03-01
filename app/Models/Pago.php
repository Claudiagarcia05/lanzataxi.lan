<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'viaje_id',
        'amount',
        'method',
        'status',
        'transaction_id',
    ];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class);
    }
}