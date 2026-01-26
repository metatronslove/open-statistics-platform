@extends('layouts.app')

@section('title', 'Doğrulama Detayları')
@section('page_title', 'Doğrulama Detayları')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.validations.index') }}">Doğrulamalar</a></li>
    <li class="breadcrumb-item active">Detaylar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Validation Info -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Doğrulama Bilgileri</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">{{ $validation->id }}</dd>
                        
                        <dt class="col-sm-4">Veri Seti:</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.datasets.show', $validation->dataset) }}">
                                {{ $validation->dataset->name }}
                            </a>
                        </dd>
                        
                        <dt class="col-sm-4">Tarih:</dt>
                        <dd class="col-sm-8">{{ $validation->date->format('d.m.Y') }}</dd>
                        
                        <dt class="col-sm-4">Durum:</dt>
                        <dd class="col-sm-8">
                            @if($validation->status == 'verified')
                                <span class="badge bg-success">Doğrulandı</span>
                            @elseif($validation->status == 'failed')
                                <span class="badge bg-danger">Başarısız</span>
                            @else
                                <span class="badge bg-warning">Bekliyor</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-4">Ortalama:</dt>
                        <dd class="col-sm-8">{{ number_format($validation->calculated_average, 4) }}</dd>
                        
                        <dt class="col-sm-4">Standart Sapma:</dt>
                        <dd class="col-sm-8">{{ number_format($validation->standard_deviation, 4) }}</dd>
                        
                        <dt class="col-sm-4">Toplam Veri:</dt>
                        <dd class="col-sm-8">{{ $validation->total_points }}</dd>
                        
                        <dt class="col-sm-4">Geçerli Veri:</dt>
                        <dd class="col-sm-8">
                            <span class="{{ $validation->valid_points == $validation->total_points ? 'text-success' : 'text-warning' }}">
                                {{ $validation->valid_points }}
                            </span>
                            ({{ $validation->total_points > 0 ? round(($validation->valid_points / $validation->total_points) * 100, 2) : 0 }}%)
                        </dd>
                        
                        <dt class="col-sm-4">Aykırı Değer:</dt>
                        <dd class="col-sm-8">
                            {{ $validation->total_points - $validation->valid_points }}
                        </dd>
                        
                        <dt class="col-sm-4">İşlem Tarihi:</dt>
                        <dd class="col-sm-8">{{ $validation->created_at->format('d.m.Y H:i') }}</dd>
                    </dl>
                    
                    @if($validation->status != 'verified')
                    <div class="mt-3">
                        <form action="{{ route('admin.validations.retry', $validation) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block" 
                                    onclick="return confirm('Bu doğrulama işlemini tekrar başlatmak istediğinizden emin misiniz?')">
                                <i class="fas fa-redo"></i> Doğrulamayı Tekrar Başlat
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Range Info -->
            <div class="card card-info mt-3">
                <div class="card-header">
                    <h3 class="card-title">Doğrulama Aralığı</h3>
                </div>
                <div class="card-body">
                    @php
                        $lowerBound = $validation->calculated_average - (2 * $validation->standard_deviation);
                        $upperBound = $validation->calculated_average + (2 * $validation->standard_deviation);
                    @endphp
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> 3 Sigma Kuralı</h5>
                        <p class="mb-0">
                            Doğrulama için kullanılan aralık: 
                            <strong>Ortalama ± 2×Standart Sapma</strong><br>
                            Geçerli aralık: <strong>{{ number_format($lowerBound, 4) }} - {{ number_format($upperBound, 4) }}</strong>
                        </p>
                    </div>
                    
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" style="width: {{ ($validation->valid_points / $validation->total_points) * 100 }}%">
                            {{ $validation->valid_points }} Geçerli
                        </div>
                        <div class="progress-bar bg-danger" style="width: {{ (($validation->total_points - $validation->valid_points) / $validation->total_points) * 100 }}%">
                            {{ $validation->total_points - $validation->valid_points }} Aykırı
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Points -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">İlgili Veri Noktaları</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sağlayıcı</th>
                                    <th>Değer</th>
                                    <th>Doğrulanmış</th>
                                    <th>Durum</th>
                                    <th>Fark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dataPoints = $validation->dataset->dataPoints
                                        ->where('date', $validation->date)
                                        ->sortByDesc('value');
                                @endphp
                                
                                @foreach($dataPoints as $dataPoint)
                                @php
                                    $diff = $dataPoint->value - $validation->calculated_average;
                                    $diffPercent = $validation->calculated_average > 0 
                                        ? abs($diff / $validation->calculated_average) * 100 
                                        : 0;
                                    $isOutlier = abs($diff) > (2 * $validation->standard_deviation);
                                @endphp
                                <tr class="{{ $isOutlier ? 'table-danger' : 'table-success' }}">
                                    <td>
                                        <strong>{{ $dataPoint->dataProvider->organization_name }}</strong>
                                        <br>
                                        <small class="text-muted">Skor: {{ $dataPoint->dataProvider->trust_score }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($dataPoint->value, 4) }}</strong>
                                        {{ $validation->dataset->unit }}
                                    </td>
                                    <td>
                                        @if($dataPoint->verified_value)
                                            {{ number_format($dataPoint->verified_value, 4) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dataPoint->is_verified)
                                            <span class="badge bg-success">Doğrulandı</span>
                                        @elseif($isOutlier)
                                            <span class="badge bg-danger">Aykırı</span>
                                        @else
                                            <span class="badge bg-warning">Bekliyor</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="{{ $diff > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 4) }}
                                            <br>
                                            <small>(%{{ number_format($diffPercent, 2) }})</small>
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Outliers List -->
            @php
                $outliers = json_decode($validation->outliers, true) ?? [];
            @endphp
            
            @if(count($outliers) > 0)
            <div class="card card-danger mt-3">
                <div class="card-header">
                    <h3 class="card-title">Aykırı Değerler</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Uyarı</h5>
                        <p class="mb-0">
                            Aşağıdaki sağlayıcıların verileri doğrulama aralığı dışında kalmıştır.
                            Bu veriler otomatik olarak reddedilmiştir.
                        </p>
                    </div>
                    
                    <ul class="list-group">
                        @foreach($outliers as $outlier)
                        <li class="list-group-item list-group-item-danger">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $outlier['provider'] ?? 'Bilinmeyen Sağlayıcı' }}</strong>
                                    <br>
                                    <small>Değer: {{ number_format($outlier['value'] ?? 0, 4) }} {{ $validation->dataset->unit }}</small>
                                </div>
                                <span class="badge bg-danger">
                                    Ortalamadan: %{{ 
                                        $validation->calculated_average > 0 
                                            ? number_format(abs(($outlier['value'] - $validation->calculated_average) / $validation->calculated_average) * 100, 2)
                                            : '0.00' 
                                    }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
