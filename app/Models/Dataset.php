<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'unit',
        'created_by',
        'calculation_rule',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dataPoints()
    {
        return $this->hasMany(DataPoint::class);
    }

    public function validationLogs()
    {
        return $this->hasMany(ValidationLog::class);
    }

    public function getVerifiedDataPoints()
    {
        return $this->dataPoints()->where('is_verified', true);
    }

    public function getLatestVerifiedValue($date = null)
    {
        $query = $this->getVerifiedDataPoints();
        
        if ($date) {
            $query->where('date', $date);
        }
        
        return $query->orderBy('date', 'desc')->first();
    }
}
