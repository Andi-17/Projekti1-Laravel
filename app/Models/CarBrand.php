<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBrand extends Model
{
    protected $table = 'cars_brands';

    protected $fillable = [
        'country',
        'is_active'
    ];

    public function models()
    {
        return $this->hasMany(CarModel::class, 'brand_id');
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'brand_id');
    }
}