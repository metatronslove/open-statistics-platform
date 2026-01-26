<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_name',
        'website',
        'description',
        'trust_score',
        'is_verified',
    ];

    protected $casts = [
        'trust_score' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataPoints()
    {
        return $this->hasMany(DataPoint::class);
    }

    public function datasets()
    {
        return $this->hasManyThrough(Dataset::class, DataPoint::class);
    }
}
