@extends('layouts.app')

@section('title', $dataset->name)
@section('page_title', $dataset->name . ' - Detaylar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item active">Görüntüle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Dataset Info -->
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.datasets.edit', $dataset) }}" class="btn btn-tool">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>İsim:</dt>
                        <dd>{{ $dataset->name }}</dd>
                        
                        <dt>Slug:</dt>
                        <dd><code>{{ $dataset->slug }}</code></dd>
                        
                        <dt>Açıklama:</dt>
                        <dd>{{ $dataset->description ?? 'Belirtilmemiş' }}</dd>
                        
                        <dt>Birim:</dt>
                        <dd>{{ $dataset->unit }}</dd>
                        
                        <dt>Oluşturan:</dt>
                        <dd>
                            {{ $dataset->creator->name }}
                            <small class="text-muted">({{ $dataset->creator->email }})</small>
                        </dd>
                        
                        <dt>Hesaplama Kuralı:</dt>
                        <dd>
                            @if($dataset->calculation_rule)
                                <code>{{ $dataset->calculation_rule }}</code>
                            @else
                                <span class="text-muted">Tanımlanmamış</span>
                            @endif
                        </dd>
                        
                        <dt>Durum:</dt>
                        <dd>
                            @if($dataset->is_public)
                                <span class="badge bg-success">Açık</span>
                                <small class="text-muted">(Tüm sağlayıcılar erişebilir)</small>
                            @else
                                <span class="badge bg-warning">Kapalı</span>
                                <small class="text-muted">(Sadece oluşturan erişebilir)</small>
                            @endif
                        </dd>
                        
                        <dt>Oluşturulma:</dt>
                        <dd>{{ $dataset->created_at->format('d.m.Y H:i') }}</dd>
                        
                        <dt>Son Güncelleme:</dt>
                        <dd>{{ $dataset->updated_at->format('d.m.Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card card-info mt-3">
                <div class="card-header">
                    <h3 class="card-title">İstatistikler</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Veri</span>
                                    <span class="info-box-number">{{ $dataset->dataPoints->count() }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doğrulanmış</span>
                                    <span class="info-box-number">
                                        {{ $dataset->dataPoints->where('is_verified', true)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bekleyen</span>
                                    <span class="info-box-number">
                                        {{ $dataset->dataPoints->where('is_verified', false)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sağlayıcı</span>
                                    <span class="info-box-number">
                                        {{ $dataset->dataPoints->groupBy('data_provider_id')->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart and Data -->
        <div class="col-md-8">
            <!-- Chart -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Doğrulanmış Veri Grafiği</h3>
                </div>
                <div class="card-body">
                    <canvas id="datasetChart" height="250"></canvas>
                </div>
            </div>

            <!-- Data Points -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Veri Noktaları</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Sağlayıcı</th>
                                    <th>Tarih</th>
                                    <th>Değer</th>
                                    <th>Doğrulanmış</th>
                                    <th>Kaynak</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataPoints as $dataPoint)
                                <tr>
                                    <td>{{ $dataPoint->dataProvider->organization_name }}</td>
                                    <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                    <td>
                                        <strong>{{ number_format($dataPoint->value, 4) }}</strong>
                                        {{ $dataset->unit }}
                                    </td>
                                    <td>
                                        @if($dataPoint->verified_value)
                                            {{ number_format($dataPoint->verified_value, 4) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dataPoint->source_url)
                                            <a href="{{ $dataPoint->source_url }}" target="_blank">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dataPoint->is_verified)
                                            <span class="badge bg-success">Doğrulandı</span>
                                        @else
                                            <span class="badge bg-warning">Bekliyor</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    {{ $dataPoints->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Logs -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Doğrulama Geçmişi</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Ortalama</th>
                                <th>Standart Sapma</th>
                                <th>Toplam Veri</th>
                                <th>Geçerli Veri</th>
                                <th>Durum</th>
                                <th>İşlem Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validationLogs as $log)
                            <tr>
                                <td>{{ $log->date->format('d.m.Y') }}</td>
                                <td>{{ number_format($log->calculated_average, 4) }}</td>
                                <td>{{ number_format($log->standard_deviation, 4) }}</td>
                                <td>{{ $log->total_points }}</td>
                                <td>{{ $log->valid_points }}</td>
                                <td>
                                    @if($log->status == 'verified')
                                        <span class="badge bg-success">Doğrulandı</span>
                                    @elseif($log->status == 'failed')
                                        <span class="badge bg-danger">Başarısız</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $validationLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('datasetChart').getContext('2d');
        
        // Chart verilerini backend'den al (şimdilik mock data)
        var verifiedData = @json($dataset->dataPoints->where('is_verified', true)->sortBy('date')->values());
        
        var labels = verifiedData.map(function(item) {
            return new Date(item.date).toLocaleDateString('tr-TR');
        });
        
        var values = verifiedData.map(function(item) {
            return parseFloat(item.verified_value || item.value);
        });
        
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ $dataset->name }} ({{ $dataset->unit }})',
                    data: values,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
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
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: '{{ $dataset->unit }}'
                        },
                        beginAtZero: false
                    }
                }
            }
        });
    });
</script>
@endpush
