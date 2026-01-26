<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'provider_id',
        'provider_name',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStatistician()
    {
        return $this->role === 'statistician';
    }

    public function isProvider()
    {
        return $this->role === 'provider';
    }

    public function dataProvider()
    {
        return $this->hasOne(DataProvider::class);
    }

    public function datasets()
    {
        return $this->hasMany(Dataset::class, 'created_by');
    }

    public function dataPoints()
    {
        return $this->hasManyThrough(DataPoint::class, DataProvider::class);
    }
}
