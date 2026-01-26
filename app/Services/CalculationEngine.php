<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use Illuminate\Support\Facades\DB;

class CalculationEngine
{
    public function calculate($dataset)
    {
        if (!$dataset->calculation_rule) {
            return null;
        }

        $rule = $dataset->calculation_rule;
        
        // Basit DSL parser
        $result = $this->parseAndCalculate($rule, $dataset);
        
        return $result;
    }

    protected function parseAndCalculate($rule, $dataset)
    {
        // Ortalama fonksiyonu: ortalama(deger)
        if (preg_match('/ortalama\(([^)]+)\)/', $rule, $matches)) {
            return $this->calculateAverage($dataset);
        }
        
        // Toplama fonksiyonu: topla(deger)
        if (preg_match('/topla\(([^)]+)\)/', $rule, $matches)) {
            return $this->calculateSum($dataset);
        }
        
        // Bölme işlemi: topla(deger) / sayi
        if (preg_match('/topla\(([^)]+)\)\s*\/\s*([\d\.]+)/', $rule, $matches)) {
            $sum = $this->calculateSum($dataset);
            $divisor = floatval($matches[2]);
            return $divisor != 0 ? $sum / $divisor : 0;
        }
        
        // Maksimum fonksiyonu: max(deger)
        if (preg_match('/max\(([^)]+)\)/', $rule, $matches)) {
            return $this->calculateMax($dataset);
        }
        
        // Minimum fonksiyonu: min(deger)
        if (preg_match('/min\(([^)]+)\)/', $rule, $matches)) {
            return $this->calculateMin($dataset);
        }
        
        // Kompleks formül: (max(deger) - min(deger)) / 2
        if (preg_match('/\(max\(([^)]+)\)\s*-\s*min\(([^)]+)\)\)\s*\/\s*2/', $rule, $matches)) {
            $max = $this->calculateMax($dataset);
            $min = $this->calculateMin($dataset);
            return ($max - $min) / 2;
        }
        
        return null;
    }

    protected function calculateAverage($dataset)
    {
        return $dataset->getVerifiedDataPoints()
            ->select(DB::raw('AVG(verified_value) as average'))
            ->value('average');
    }

    protected function calculateSum($dataset)
    {
        return $dataset->getVerifiedDataPoints()
            ->select(DB::raw('SUM(verified_value) as total'))
            ->value('total');
    }

    protected function calculateMax($dataset)
    {
        return $dataset->getVerifiedDataPoints()
            ->select(DB::raw('MAX(verified_value) as max_value'))
            ->value('max_value');
    }

    protected function calculateMin($dataset)
    {
        return $dataset->getVerifiedDataPoints()
            ->select(DB::raw('MIN(verified_value) as min_value'))
            ->value('min_value');
    }

    protected function calculateCount($dataset)
    {
        return $dataset->getVerifiedDataPoints()->count();
    }
}
