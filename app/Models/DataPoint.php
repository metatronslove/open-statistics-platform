<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataset_id',
        'data_provider_id',
        'date',
        'value',
        'source_url',
        'is_verified',
        'verified_value',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:4',
        'verified_value' => 'decimal:4',
        'is_verified' => 'boolean',
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }

    public function dataProvider()
    {
        return $this->belongsTo(DataProvider::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDataset($query, $datasetId)
    {
        return $query->where('dataset_id', $datasetId);
    }
}
