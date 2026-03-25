<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarImage extends Model
{
    protected $fillable = [
        'car_id',
        'url',
        'type',
        'size'
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}