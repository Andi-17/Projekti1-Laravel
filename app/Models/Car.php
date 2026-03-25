<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{protected $fillable = [
    'brand_id',
    'model_id',
    'title',
    'description',
    'year',
    'color',
    'fuel_type',
    'transmission',
    'engine',
    'horsepower',
    'mileage',
    'price',
    'currency',
    'status',
    'featured',
    'main_image'
];

    public function brand()
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class);
    }

    public function images()
    {
        return $this->hasMany(CarImage::class);
    }
}