@extends('layouts.app')

@section('title', 'Hesaplama Detayları: ' . $dataset->name)
@section('page_title', 'Hesaplama Detayları: ' . $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.calculations.index') }}">Hesaplamalar</a></li>
    <li class="breadcrumb-item active">Detaylar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Calculation Result -->
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Hesaplama Sonucu</h3>
                </div>
                <div class="card-body text-center">
                    @if($result !== null)
                        <div class="display-4 text-success mb-3">
                            {{ number_format($result, 4) }}
                            <small class="text-muted">{{ $dataset->unit }}</small>
                        </div>
                        
                        <div class="alert alert-light">
                            <strong>Hesaplama Kuralı:</strong>
                            <code class="d-block mt-2 p-2 bg-white rounded">
                                {{ $dataset->calculation_rule }}
                            </code>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h4><i class="fas fa-exclamation-triangle"></i> Hesaplanamadı</h4>
                            <p class="mb-0">
                                Bu veri seti için hesaplama yapılamadı.
                                Lütfen hesaplama kuralını kontrol edin veya yeterli veri olduğundan emin olun.
                            </p>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Kuralı Düzenle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dataset Info -->
            <div class="card card-info mt-3">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>İsim:</dt>
                        <dd>{{ $dataset->name }}</dd>
                        
                        <dt>Açıklama:</dt>
                        <dd>{{ $dataset->description ?? 'Belirtilmemiş' }}</dd>
                        
                        <dt>Birim:</dt>
                        <dd>{{ $dataset->unit }}</dd>
                        
                        <dt>Veri Noktası:</dt>
                        <dd>{{ $dataset->dataPoints()->count() }}</dd>
                        
                        <dt>Doğrulanmış Veri:</dt>
                        <dd>{{ $dataset->dataPoints()->where('is_verified', true)->count() }}</dd>
                        
                        <dt>Durum:</dt>
                        <dd>
                            @if($dataset->is_public)
                                <span class="badge bg-success">Açık</span>
                            @else
                                <span class="badge bg-warning">Kapalı</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Calculation History -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hesaplama Geçmişi (Son 30 Gün)</h3>
                </div>
                <div class="card-body">
                    @if(empty($history))
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Geçmiş Veri Yok</h5>
                            <p class="mb-0">
                                Bu veri seti için son 30 günlük hesaplama geçmişi bulunmuyor.
                                Bu, yeni bir veri seti olabilir veya yeterli veri bulunmuyor olabilir.
                            </p>
                        </div>
                    @else
                        <div class="chart-container">
                            <canvas id="calculationHistoryChart"></canvas>
                        </div>
                        
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Değer</th>
                                        <th>Veri Sayısı</th>
                                        <th>Değişim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $previousValue = null;
                                    @endphp
                                    @foreach($history as $record)
                                    <tr>
                                        <td>{{ $record['date']->format('d.m.Y') }}</td>
                                        <td>
                                            <strong>{{ number_format($record['value'], 4) }}</strong>
                                            {{ $dataset->unit }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $record['count'] }}</span>
                                        </td>
                                        <td>
                                            @if($previousValue !== null)
                                                @php
                                                    $change = $record['value'] - $previousValue;
                                                    $changePercent = $previousValue != 0 
                                                        ? ($change / $previousValue) * 100 
                                                        : 0;
                                                @endphp
                                                <span class="{{ $change > 0 ? 'text-success' : ($change < 0 ? 'text-danger' : 'text-muted') }}">
                                                    {{ $change > 0 ? '+' : '' }}{{ number_format($change, 4) }}
                                                    <br>
                                                    <small>(%{{ number_format($changePercent, 2) }})</small>
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $previousValue = $record['value'];
                                    @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Data Points Used -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Hesaplamada Kullanılan Veriler</h3>
                </div>
                <div class="card-body">
                    @php
                        $dataPoints = $dataset->dataPoints()
                            ->where('is_verified', true)
                            ->orderBy('date', 'desc')
                            ->limit(50)
                            ->get();
                    @endphp
                    
                    @if($dataPoints->isEmpty())
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Doğrulanmış Veri Yok</h5>
                            <p class="mb-0">
                                Bu veri setinde henüz doğrulanmış veri bulunmuyor.
                                Hesaplama yapabilmek için önce verilerin doğrulanması gerekiyor.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Sağlayıcı</th>
                                        <th>Değer</th>
                                        <th>Doğrulanma Tarihi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataPoints as $dataPoint)
                                    <tr>
                                        <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                        <td>{{ $dataPoint->dataProvider->organization_name }}</td>
                                        <td>
                                            <strong>{{ number_format($dataPoint->verified_value ?? $dataPoint->value, 4) }}</strong>
                                            {{ $dataset->unit }}
                                        </td>
                                        <td>{{ $dataPoint->updated_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-2">
                            <a href="{{ route('statistician.datasets.show', $dataset) }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> Tüm Verileri Gör
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(!empty($history))
            var ctx = document.getElementById('calculationHistoryChart').getContext('2d');
            
            var labels = @json(array_map(function($record) {
                return $record['date']->format('d.m.Y');
            }, $history));
            
            var values = @json(array_map(function($record) {
                return $record['value'];
            }, $history));
            
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ $dataset->name }} ({{ $dataset->unit }})',
                        data: values,
                        borderColor: 'rgb(40, 167, 69)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: 'rgb(40, 167, 69)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(4);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tarih'
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: '{{ $dataset->unit }}'
                            },
                            beginAtZero: false
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        @endif
    });
</script>
@endpush
