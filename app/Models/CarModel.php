<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $table = 'cars_models';

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'year_start',
        'year_end',
        'is_active'
    ];

    public function brand()
    {
        return $this->belongsTo(CarBrand::class, 'brand_id');
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'model_id');
    }
}