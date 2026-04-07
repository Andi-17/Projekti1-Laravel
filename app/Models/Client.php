<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'client_type',

        'first_name',
        'last_name',

        'business_name',
        'additional_company_name',

        'street',
        'building_number',
        'additional_address_info',
        'city',
        'country',

        'client_language',
        'remarks',
        'category',
        'employees_count',

        'email',
        'phone',
        'mobile',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    
    public function isBusiness()
    {
        return $this->client_type === 'business';
    }

    public function isPrivate()
    {
        return $this->client_type === 'private';
    }
}