<?php

namespace App\Console\Commands;

use App\Models\Dataset;
use App\Services\CalculationEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateRulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rules:calculate 
                            {--dataset= : Belirli bir veri seti ID}
                            {--all : Tüm veri setlerini hesapla}
                            {--force : Hata olsa da devam et}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm hesaplama kurallarını çalıştır ve sonuçları kaydet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Hesaplama kuralları çalıştırılıyor...');
        
        $datasetOption = $this->option('dataset');
        $allOption = $this->option('all');
        $forceOption = $this->option('force');
        
        $query = Dataset::whereNotNull('calculation_rule');
        
        if ($datasetOption) {
            $query->where('id', $datasetOption);
        }
        
        $datasets = $query->get();
        
        if ($datasets->isEmpty()) {
            $this->warn('Hesaplanacak veri seti bulunamadı.');
            return;
        }
        
        $this->info("{$datasets->count()} veri seti bulundu.");
        
        $calculationEngine = new CalculationEngine();
        $successCount = 0;
        $errorCount = 0;
        
        $progressBar = $this->output->createProgressBar($datasets->count());
        $progressBar->start();
        
        foreach ($datasets as $dataset) {
            try {
                $result = $calculationEngine->calculate($dataset);
                
                if ($result !== null) {
                    // Sonucu logla veya kaydet
                    $this->logCalculation($dataset, $result);
                    $successCount++;
                    
                    if ($this->getOutput()->isVerbose()) {
                        $this->line("\n[OK] {$dataset->name}: {$result}");
                    }
                } else {
                    $errorCount++;
                    $this->warn("\n[ERROR] {$dataset->name}: Hesaplanamadı");
                    
                    if (!$forceOption) {
                        $this->error('İşlem durduruldu. Devam etmek için --force kullanın.');
                        break;
                    }
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Rule calculation failed', [
                    'dataset_id' => $dataset->id,
                    'error' => $e->getMessage(),
                ]);
                
                $this->error("\n[EXCEPTION] {$dataset->name}: " . $e->getMessage());
                
                if (!$forceOption) {
                    $this->error('İşlem durduruldu. Devam etmek için --force kullanın.');
                    break;
                }
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        
        $this->newLine(2);
        $this->info('Hesaplama tamamlandı!');
        $this->table(
            ['Durum', 'Sayı'],
            [
                ['Başarılı', $successCount],
                ['Hatalı', $errorCount],
                ['Toplam', $datasets->count()],
            ]
        );
        
        if ($successCount > 0) {
            $this->info("{$successCount} veri seti başarıyla hesaplandı.");
        }
        
        if ($errorCount > 0) {
            $this->warn("{$errorCount} veri setinde hata oluştu.");
        }
    }
    
    /**
     * Hesaplama sonucunu logla
     */
    protected function logCalculation($dataset, $result)
    {
        // Hesaplama geçmişini kaydetmek için
        Log::info('Rule calculated', [
            'dataset_id' => $dataset->id,
            'dataset_name' => $dataset->name,
            'rule' => $dataset->calculation_rule,
            'result' => $result,
            'calculated_at' => now(),
        ]);
        
        // İsterseniz veritabanına da kaydedebilirsiniz
        // CalculationLog::create([...]);
    }
    
    /**
     * Command için yardım bilgisi
     */
    public function getHelp()
    {
        return <<<HELP
Hesaplama kurallarını çalıştırır.
        
Kullanım örnekleri:
  php artisan rules:calculate --all          Tüm veri setlerini hesapla
  php artisan rules:calculate --dataset=1    Belirli bir veri setini hesapla
  php artisan rules:calculate --force        Hatalarda durmadan devam et
        
Seçenekler:
  --dataset=ID    Hesaplanacak veri seti ID'si
  --all           Tüm veri setlerini hesapla
  --force         Hata olsa da devam et
HELP;
    }
}
