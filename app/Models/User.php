<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    public function installationsOwned()
{
    return $this->hasMany(Installation::class, 'client_id');
}

public function assignedInstallations()
{
    return $this->belongsToMany(Installation::class, 'installation_technician', 'technician_id', 'installation_id')
        ->withTimestamps()
        ->withPivot('assigned_at');
}
   
    use HasFactory, Notifiable, HasApiTokens;

   
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'expo_push_token'
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

  
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

