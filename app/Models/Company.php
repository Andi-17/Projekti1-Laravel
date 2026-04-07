<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'business_number',
        'vat_number',
        'owner_id',
        'logo', 
    ];


  
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function cars()
{
    return $this->hasMany(\App\Models\Car::class);
}
}