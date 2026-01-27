@extends('layouts.app')

@section('title', 'Veri Sağlayıcı Dashboard')
@section('page_title', 'Veri Sağlayıcı Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$dataProvider)
        <div class="alert alert-warning">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Profil Tamamlanmamış</h5>
            Veri girişi yapabilmek için önce profilinizi tamamlamanız gerekiyor.
            <a href="{{ route('provider.profile') }}" class="btn btn-warning btn-sm ml-3">Profili Tamamla</a>
        </div>
    @endif
    
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $verifiedDataPoints }}</h3>
                    <p>Doğrulanmış Veri</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('provider.data-entry.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingDataPoints }}</h3>
                    <p>Bekleyen Doğrulama</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('provider.data-entry.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $availableDatasets->count() }}</h3>
                    <p>Veri Girebileceğim Setler</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
                <a href="{{ route('provider.data-entry.create') }}" class="small-box-footer">
                    Veri Gir <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>
                        @if($dataProvider && $dataProvider->is_verified)
                            <i class="fas fa-check"></i>
                        @else
                            <i class="fas fa-times"></i>
                        @endif
                    </h3>
                    <p>Doğrulama Durumu</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('provider.profile') }}" class="small-box-footer">
                    Profilim <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    @if($dataProvider)
    <div class="row">
        <!-- Available Datasets -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Veri Girebileceğim Veri Setleri</h3>
                </div>
                <div class="card-body">
                    @foreach($availableDatasets as $dataset)
                    <div class="callout callout-info">
                        <h5>{{ $dataset->name }}</h5>
                        <p>{{ Str::limit($dataset->description, 100) }}</p>
                        <div class="row">
                            <div class="col-6">
                                <small>Birim: {{ $dataset->unit }}</small>
                            </div>
                            <div class="col-6 text-right">
                                @if($dataset->dataPoints->count() > 0)
                                    <small>Son giriş: {{ $dataset->dataPoints->first()->date->format('d.m.Y') }}</small>
                                @else
                                    <small>Henüz veri girilmedi</small>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('provider.data-entry.create') }}?dataset={{ $dataset->id }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Veri Ekle
                            </a>
                            <a href="{{ route('provider.data-entry.index') }}?dataset={{ $dataset->id }}" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-list"></i> Verilerim
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Recent Data Points -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Eklediğim Veriler</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Tarih</th>
                                <th>Değer</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->dataset->name }}</td>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>{{ number_format($dataPoint->value, 2) }} {{ $dataPoint->dataset->unit }}</td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('provider.data-entry.edit', $dataPoint) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('provider.data-entry.index') }}" class="btn btn-primary btn-sm">
                        Tüm Verilerim
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Provider Info -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Veri Sağlayıcı Bilgilerim</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Kurum Adı:</strong> {{ $dataProvider->organization_name }}</p>
                            @if($dataProvider->website)
                                <p><strong>Website:</strong> 
                                    <a href="{{ $dataProvider->website }}" target="_blank">{{ $dataProvider->website }}</a>
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Doğrulama Durumu:</strong> 
                                @if($dataProvider->is_verified)
                                    <span class="badge bg-success">Doğrulanmış</span>
                                @else
                                    <span class="badge bg-warning">Doğrulama Bekliyor</span>
                                @endif
                            </p>
                            <p><strong>Güven Skoru:</strong> 
                                <span class="badge bg-{{ $dataProvider->trust_score >= 80 ? 'success' : ($dataProvider->trust_score >= 60 ? 'warning' : 'danger') }}">
                                    {{ number_format($dataProvider->trust_score, 1) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($dataProvider->description)
                        <hr>
                        <p><strong>Açıklama:</strong></p>
                        <p>{{ $dataProvider->description }}</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('provider.profile') }}" class="btn btn-info">
                        <i class="fas fa-edit"></i> Profili Düzenle
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
