<?php

namespace App\Services;

use App\Models\Dataset;
use Illuminate\Support\Str;

class RuleEvaluationService
{
    protected $availableFunctions = [
        'avg', 'mean', 'sum', 'count', 'min', 'max', 'last', 'diff', 'rate', 'stddev'
    ];
    
    protected $availableOperators = ['+', '-', '*', '/', '(', ')'];
    
    /**
     * DSL ifadesini değerlendir
     */
    public function evaluate($expression, $datasetId)
    {
        // Temizle ve normalize et
        $expression = Str::lower(trim($expression));
        
        // Dataset'i yükle
        $dataset = Dataset::with(['dataPoints' => function ($query) {
            $query->where('is_verified', true)
                  ->orderBy('date', 'desc');
        }])->findOrFail($datasetId);
        
        // Dataset slug'ını değiştir
        $expression = str_replace($dataset->slug, 'dataset', $expression);
        
        // Fonksiyonları işle
        foreach ($this->availableFunctions as $function) {
            if (Str::contains($expression, $function)) {
                $expression = $this->evaluateFunction($expression, $function, $dataset);
            }
        }
        
        // Matematiksel ifadeyi değerlendir
        try {
            // Güvenli eval
            $result = $this->safeEval($expression);
            return is_numeric($result) ? (float) $result : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Fonksiyonları değerlendir
     */
    protected function evaluateFunction($expression, $function, $dataset)
    {
        $pattern = '/(' . $function . ')\(([^)]+)\)/';
        
        while (preg_match($pattern, $expression, $matches)) {
            $fullMatch = $matches[0];
            $params = $matches[2];
            
            $value = $this->callFunction($function, $params, $dataset);
            
            // Fonksiyon çağrısını değeriyle değiştir
            $expression = str_replace($fullMatch, (string) $value, $expression);
        }
        
        return $expression;
    }
    
    /**
     * Fonksiyonu çağır
     */
    protected function callFunction($function, $params, $dataset)
    {
        $dataPoints = $dataset->dataPoints;
        
        if ($dataPoints->isEmpty()) {
            return 0;
        }
        
        $values = $dataPoints->pluck('verified_value')->toArray();
        
        switch ($function) {
            case 'avg':
            case 'mean':
                return array_sum($values) / count($values);
                
            case 'sum':
                return array_sum($values);
                
            case 'count':
                return count($values);
                
            case 'min':
                return min($values);
                
            case 'max':
                return max($values);
                
            case 'last':
                $n = is_numeric($params) ? (int) $params : 1;
                $lastValues = array_slice($values, 0, $n);
                return array_sum($lastValues) / count($lastValues);
                
            case 'diff':
                if (count($values) >= 2) {
                    return end($values) - reset($values);
                }
                return 0;
                
            case 'rate':
                if (count($values) >= 2) {
                    $first = reset($values);
                    $last = end($values);
                    return $first != 0 ? (($last - $first) / $first) * 100 : 0;
                }
                return 0;
                
            case 'stddev':
                $mean = array_sum($values) / count($values);
                $sum = 0;
                foreach ($values as $value) {
                    $sum += pow($value - $mean, 2);
                }
                return sqrt($sum / count($values));
                
            default:
                return 0;
        }
    }
    
    /**
     * Güvenli matematiksel ifade değerlendirme
     */
    protected function safeEval($expression)
    {
        // Sadece sayılar, nokta, operatörler ve boşluk
        $cleanExpression = preg_replace('/[^0-9\.\+\-\*\/\(\)\s]/', '', $expression);
        
        // Boş ifade kontrolü
        if (empty(trim($cleanExpression))) {
            return 0;
        }
        
        // Matematiksel ifadeyi değerlendir
        $result = 0;
        eval('$result = ' . $cleanExpression . ';');
        
        return $result;
    }
    
    /**
     * DSL ifadesini doğrula
     */
    public function validateExpression($expression)
    {
        $errors = [];
        
        // Boş kontrolü
        if (empty(trim($expression))) {
            $errors[] = 'İfade boş olamaz.';
            return $errors;
        }
        
        // Geçersiz karakter kontrolü
        $invalidChars = preg_match('/[^a-zA-Z0-9\.\+\-\*\/\(\)_\s]/', $expression);
        if ($invalidChars) {
            $errors[] = 'İfade geçersiz karakterler içeriyor.';
        }
        
        // Parantez kontrolü
        $openParentheses = substr_count($expression, '(');
        $closeParentheses = substr_count($expression, ')');
        if ($openParentheses !== $closeParentheses) {
            $errors[] = 'Parantezler eşleşmiyor.';
        }
        
        // Fonksiyon kontrolü
        preg_match_all('/([a-z]+)\(/', $expression, $functionMatches);
        if (!empty($functionMatches[1])) {
            foreach ($functionMatches[1] as $function) {
                if (!in_array($function, $this->availableFunctions)) {
                    $errors[] = "Bilinmeyen fonksiyon: {$function}";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Örnek ifadeler getir
     */
    public function getExampleExpressions()
    {
        return [
            'Ortalama hesaplama' => 'avg(dataset)',
            'Toplam' => 'sum(dataset)',
            'Değişim oranı' => 'rate(dataset)',
            'Standart sapma' => 'stddev(dataset)',
            'Kompleks ifade' => '(max(dataset) - min(dataset)) / avg(dataset) * 100',
            'Son 5 değer ortalaması' => 'last(dataset, 5)',
            'Fark hesaplama' => 'diff(dataset)',
        ];
    }
}
