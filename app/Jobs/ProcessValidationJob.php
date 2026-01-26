<?php

namespace App\Jobs;

use App\Models\Dataset;
use App\Services\DataVerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessValidationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $datasetId;
    protected $date;

    public function __construct($datasetId, $date)
    {
        $this->datasetId = $datasetId;
        $this->date = $date;
    }

    public function handle()
    {
        $dataset = Dataset::find($this->datasetId);
        
        if (!$dataset) {
            return;
        }

        $service = new DataVerificationService();
        $service->processValidation($dataset, $this->date);
    }
}
