<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens; // <--- Sigurohu që kjo është këtu
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password', 'role_id', 'avatar'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
   

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function roles()
{
    return $this->belongsToMany(Role::class);
}

    public function companies()
    {
        return $this->hasMany(Company::class, 'owner_id');
    }
}
