@extends('layouts.app')

@section('title', $dataset->name . ' Detay')
@section('page_title', $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('statistician.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item active">{{ $dataset->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Dataset Info Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                    <div class="card-tools">
                        <span class="badge bg-{{ $dataset->is_public ? 'success' : 'secondary' }}">
                            {{ $dataset->is_public ? 'Açık' : 'Kapalı' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Oluşturan:</strong> {{ $dataset->creator->name }}</p>
                            <p><strong>Birim:</strong> {{ $dataset->unit }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Oluşturulma:</strong> {{ $dataset->created_at->format('d.m.Y H:i') }}</p>
                            <p><strong>Son Güncelleme:</strong> {{ $dataset->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($dataset->description)
                        <hr>
                        <p><strong>Açıklama:</strong></p>
                        <p>{{ $dataset->description }}</p>
                    @endif
                    
                    @if($dataset->calculation_rule)
                        <hr>
                        <p><strong>Hesaplama Kuralı:</strong></p>
                        <div class="alert alert-info">
                            <code>{{ $dataset->calculation_rule }}</code>
                        </div>
                        @if($calculatedValue !== null)
                            <p><strong>Hesaplanan Değer:</strong> 
                                <span class="badge bg-success" style="font-size: 1.2em;">
                                    {{ number_format($calculatedValue, 2) }} {{ $dataset->unit }}
                                </span>
                            </p>
                        @endif
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                    
                    <!-- Manual Verification Form -->
                    <form action="{{ route('statistician.datasets.verify', $dataset) }}" method="POST" class="d-inline">
                        @csrf
                        <div class="input-group input-group-sm" style="width: 300px; display: inline-flex;">
                            <input type="date" class="form-control" name="date" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-check"></i> Doğrula
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Chart Card -->
            <div class="card card-success mt-3">
                <div class="card-header">
                    <h3 class="card-title">Veri Grafiği</h3>
                </div>
                <div class="card-body">
                    <canvas id="datasetChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Stats Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">İstatistikler</h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Toplam Veri Noktası</span>
                            <span class="info-box-number">{{ $dataPoints->total() }}</span>
                        </div>
                    </div>
                    
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Doğrulanmış Veri</span>
                            <span class="info-box-number">
                                {{ $dataset->dataPoints()->where('is_verified', true)->count() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Bekleyen Doğrulama</span>
                            <span class="info-box-number">
                                {{ $dataset->dataPoints()->where('is_verified', false)->count() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Veri Sağlayıcı Sayısı</span>
                            <span class="info-box-number">
                                {{ $dataset->dataPoints()->distinct('data_provider_id')->count('data_provider_id') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Validations -->
            <div class="card card-warning mt-3">
                <div class="card-header">
                    <h3 class="card-title">Son Doğrulamalar</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @foreach($validationLogs as $log)
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                {{ $log->date->format('d.m.Y') }}
                                <span class="float-right badge bg-{{ $log->status === 'verified' ? 'success' : ($log->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ $log->valid_points }}/{{ $log->total_points }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Points Table -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Veri Noktaları</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Ara...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Veri Sağlayıcı</th>
                                <th>Değer</th>
                                <th>Doğrulanmış Değer</th>
                                <th>Kaynak</th>
                                <th>Durum</th>
                                <th>Notlar</th>
                                <th>İşlem Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>{{ $dataPoint->dataProvider->organization_name ?? '-' }}</td>
                                <td>{{ number_format($dataPoint->value, 4) }}</td>
                                <td>
                                    @if($dataPoint->verified_value)
                                        {{ number_format($dataPoint->verified_value, 4) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->source_url)
                                        <a href="{{ $dataPoint->source_url }}" target="_blank" class="btn btn-xs btn-info">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($dataPoint->notes, 30) }}</td>
                                <td>{{ $dataPoint->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $dataPoints->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Chart Data
        const chartData = @json($chartData);
        
        // Create Chart
        const ctx = document.getElementById('datasetChart').getContext('2d');
        const datasetChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: '{{ $dataset->name }} ({{ $dataset->unit }})',
                    data: chartData.values,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
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
                                return context.dataset.label + ': ' + context.parsed.y + ' {{ $dataset->unit }}';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: '{{ $dataset->unit }}'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tarih'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
