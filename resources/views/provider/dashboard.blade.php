@extends('layouts.app')

@section('title', 'Veri Sağlayıcı Dashboard')
@section('page_title', 'Veri Sağlayıcı Paneli')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$dataProvider)
        <div class="alert alert-warning">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Profil Eksik!</h5>
            <p>Veri girişi yapabilmek için önce profilinizi tamamlamanız gerekmektedir.</p>
            <a href="{{ route('provider.profile') }}" class="btn btn-warning">
                <i class="fas fa-user-edit"></i> Profili Tamamla
            </a>
        </div>
    @else
        <!-- Hızlı İstatistikler -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
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
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $verifiedDataPoints }}</h3>
                        <p>Doğrulanmış Verilerim</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('provider.data-entry.index') }}" class="small-box-footer">
                        Tümünü Gör <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pendingDataPoints }}</h3>
                        <p>Doğrulama Bekleyen</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('provider.data-entry.index') }}" class="small-box-footer">
                        İncele <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $dataProvider->trust_score }}</h3>
                        <p>Güven Skorum</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <a href="{{ route('provider.profile') }}" class="small-box-footer">
                        Profilim <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Veri Sağlayıcı Bilgileri -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Profil Bilgilerim</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Kurum Adı:</dt>
                            <dd class="col-sm-8">{{ $dataProvider->organization_name }}</dd>
                            
                            <dt class="col-sm-4">Web Sitesi:</dt>
                            <dd class="col-sm-8">
                                @if($dataProvider->website)
                                    <a href="{{ $dataProvider->website }}" target="_blank">
                                        {{ $dataProvider->website }}
                                    </a>
                                @else
                                    Belirtilmemiş
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Açıklama:</dt>
                            <dd class="col-sm-8">{{ $dataProvider->description ?? 'Belirtilmemiş' }}</dd>
                            
                            <dt class="col-sm-4">Doğrulama Durumu:</dt>
                            <dd class="col-sm-8">
                                @if($dataProvider->is_verified)
                                    <span class="badge bg-success">Doğrulanmış</span>
                                @else
                                    <span class="badge bg-warning">Bekliyor</span>
                                @endif
                            </dd>
                            
                            <dt class="col-sm-4">Güven Skoru:</dt>
                            <dd class="col-sm-8">
                                <div class="progress">
                                    <div class="progress-bar 
                                        @if($dataProvider->trust_score >= 80) bg-success
                                        @elseif($dataProvider->trust_score >= 60) bg-warning
                                        @else bg-danger @endif" 
                                        style="width: {{ $dataProvider->trust_score }}%">
                                        {{ $dataProvider->trust_score }}
                                    </div>
                                </div>
                            </dd>
                        </dl>
                        <a href="{{ route('provider.profile') }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Profili Düzenle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hızlı Veri Girişi -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Hızlı Veri Girişi</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('provider.data-entry.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="dataset_id">Veri Seti *</label>
                                <select name="dataset_id" id="dataset_id" class="form-control" required>
                                    <option value="">Seçiniz</option>
                                    @foreach($availableDatasets as $dataset)
                                        <option value="{{ $dataset->id }}">
                                            {{ $dataset->name }} ({{ $dataset->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="date">Tarih *</label>
                                <input type="date" name="date" id="date" 
                                       class="form-control" 
                                       value="{{ date('Y-m-d') }}"
                                       max="{{ date('Y-m-d') }}"
                                       required>
                            </div>
                            
                            <div class="form-group">
                                <label for="value">Değer *</label>
                                <input type="number" name="value" id="value" 
                                       class="form-control" step="0.0001" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="source_url">Kaynak URL</label>
                                <input type="url" name="source_url" id="source_url" 
                                       class="form-control" placeholder="https://...">
                            </div>
                            
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Veriyi Kaydet
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Eklenen Verilerim -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Son Eklediğim Veriler</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Veri Seti</th>
                                    <th>Tarih</th>
                                    <th>Değer</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDataPoints as $dataPoint)
                                <tr>
                                    <td>{{ $dataPoint->dataset->name }}</td>
                                    <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                    <td>
                                        <strong>{{ number_format($dataPoint->value, 4) }}</strong>
                                        {{ $dataPoint->dataset->unit }}
                                    </td>
                                    <td>
                                        @if($dataPoint->is_verified)
                                            <span class="badge bg-success">Doğrulandı</span>
                                        @else
                                            <span class="badge bg-warning">Bekliyor</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('provider.data-entry.edit', $dataPoint) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @if($recentDataPoints->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="alert alert-info m-0">
                                            Henüz veri girişi yapmadınız.
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('provider.data-entry.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> Tüm Verilerimi Gör
                        </a>
                        <a href="{{ route('provider.data-entry.create') }}" class="btn btn-success float-right">
                            <i class="fas fa-plus"></i> Yeni Veri Gir
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mevcut Veri Setleri -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Veri Girebileceğim Setler</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($availableDatasets as $dataset)
                            <div class="col-md-4 mb-3">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ $dataset->name }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ Str::limit($dataset->description, 100) }}</p>
                                        <p><strong>Birim:</strong> {{ $dataset->unit }}</p>
                                        <p><strong>Son Verim:</strong> 
                                            @if($dataset->dataPoints->isNotEmpty())
                                                {{ number_format($dataset->dataPoints->first()->value, 4) }} 
                                                ({{ $dataset->dataPoints->first()->date->format('d.m.Y') }})
                                            @else
                                                Henüz yok
                                            @endif
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('provider.data-entry.create') }}?dataset_id={{ $dataset->id }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Veri Gir
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
