<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataset_id',
        'date',
        'calculated_average',
        'standard_deviation',
        'status',
        'outliers',
        'total_points',
        'valid_points',
    ];

    protected $casts = [
        'date' => 'date',
        'calculated_average' => 'decimal:4',
        'standard_deviation' => 'decimal:4',
        'outliers' => 'array',
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }
}
