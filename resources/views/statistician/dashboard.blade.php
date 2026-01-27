@extends('layouts.app')

@section('title', 'İstatistikçi Dashboard')
@section('page_title', 'İstatistikçi Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $myDatasets->count() }}</h3>
                    <p>Veri Setlerim</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
                <a href="{{ route('statistician.datasets.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $pendingValidations->count() }}</h3>
                    <p>Bekleyen Doğrulama</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('statistician.datasets.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ count($calculatedValues) }}</h3>
                    <p>Hesaplanan Değer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <a href="{{ route('statistician.calculations.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    @php
                        $totalDataPoints = 0;
                        foreach($myDatasets as $dataset) {
                            $totalDataPoints += $dataset->data_points_count;
                        }
                    @endphp
                    <h3>{{ $totalDataPoints }}</h3>
                    <p>Toplam Veri Noktası</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <a href="{{ route('statistician.datasets.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Data Points -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Eklenen Veri Noktaları</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Sağlayıcı</th>
                                <th>Tarih</th>
                                <th>Değer</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->dataset->name }}</td>
                                <td>{{ $dataPoint->dataProvider->organization_name ?? '-' }}</td>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>{{ number_format($dataPoint->value, 2) }} {{ $dataPoint->dataset->unit }}</td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulanmış</span>
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
        </div>
        
        <!-- Calculated Values -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hesaplanan Değerler</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Hesaplanan Değer</th>
                                <th>Birim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculatedValues as $value)
                            <tr>
                                <td>{{ $value['name'] }}</td>
                                <td>
                                    @if($value['value'] !== null)
                                        <strong>{{ number_format($value['value'], 2) }}</strong>
                                    @else
                                        <span class="text-muted">Hesaplanamadı</span>
                                    @endif
                                </td>
                                <td>{{ $value['unit'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('statistician.calculations.index') }}" class="btn btn-primary btn-sm">
                        Tüm Hesaplamalar
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- My Datasets -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Veri Setlerim</h3>
                    <div class="card-tools">
                        <a href="{{ route('statistician.datasets.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Veri Seti
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>İsim</th>
                                <th>Açıklama</th>
                                <th>Veri Noktası</th>
                                <th>Doğrulama</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myDatasets as $dataset)
                            <tr>
                                <td>{{ $dataset->name }}</td>
                                <td>{{ Str::limit($dataset->description, 50) }}</td>
                                <td>{{ $dataset->data_points_count }}</td>
                                <td>{{ $dataset->validation_logs_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $dataset->is_public ? 'success' : 'secondary' }}">
                                        {{ $dataset->is_public ? 'Açık' : 'Kapalı' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('statistician.datasets.show', $dataset) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('statistician.datasets.index') }}" class="btn btn-primary">Tüm Veri Setleri</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
