<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'license_number', 'rating', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taxi()
    {
        return $this->hasOne(Taxi::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
