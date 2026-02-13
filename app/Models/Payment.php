<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['trip_id', 'amount', 'method', 'status'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
