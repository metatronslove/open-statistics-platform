@extends('layouts.app')

@section('title', $dataset->name)
@section('page_title', $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.datasets.index') }}">Veri Setlerim</a></li>
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
                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-tool">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>İsim:</dt>
                        <dd>{{ $dataset->name }}</dd>
                        
                        <dt>Açıklama:</dt>
                        <dd>{{ $dataset->description ?? 'Belirtilmemiş' }}</dd>
                        
                        <dt>Birim:</dt>
                        <dd>{{ $dataset->unit }}</dd>
                        
                        <dt>Hesaplama Kuralı:</dt>
                        <dd>
                            @if($dataset->calculation_rule)
                                <code>{{ $dataset->calculation_rule }}</code>
                                <br>
                                <small class="text-muted">Hesaplanan Değer: 
                                    <strong>{{ number_format($calculatedValue, 4) ?? 'Hesaplanamadı' }}</strong>
                                </small>
                            @else
                                <span class="text-muted">Tanımlanmamış</span>
                            @endif
                        </dd>
                        
                        <dt>Durum:</dt>
                        <dd>
                            @if($dataset->is_public)
                                <span class="badge bg-success">Açık</span>
                            @else
                                <span class="badge bg-warning">Kapalı</span>
                            @endif
                        </dd>
                        
                        <dt>Oluşturulma:</dt>
                        <dd>{{ $dataset->created_at->format('d.m.Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Verification Form -->
            <div class="card card-warning mt-3">
                <div class="card-header">
                    <h3 class="card-title">Veri Doğrulama</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('statistician.datasets.verify', $dataset) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="date">Doğrulama Tarihi</label>
                            <input type="date" class="form-control" id="date" name="date" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-check-circle"></i> Verileri Doğrula
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-md-8">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Doğrulanmış Veri Grafiği</h3>
                </div>
                <div class="card-body">
                    <canvas id="datasetChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Points -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Veri Noktaları</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sağlayıcı</th>
                                <th>Tarih</th>
                                <th>Değer</th>
                                <th>Doğrulanmış Değer</th>
                                <th>Kaynak</th>
                                <th>Durum</th>
                                <th>Eklenme</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->id }}</td>
                                <td>{{ $dataPoint->dataProvider->organization_name }}</td>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
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
                                <td>{{ $dataPoint->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                <div class="card-body table-responsive p-0">
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
        var chartData = @json($chartData);
        
        var chart = new Chart(ctx, {
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
