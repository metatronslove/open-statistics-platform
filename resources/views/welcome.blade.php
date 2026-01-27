@extends('layouts.app')

@section('title', 'Open Statistics for Economy')
@section('page_title', 'Açık İstatistik Platformu')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="jumbotron bg-light p-5 rounded">
                <h1 class="display-4">Open Statistics for Economy</h1>
                <p class="lead">TÜİK'in 2016 öncesi metodolojisiyle, şeffaf, çoklu kaynaktan veri toplayan ve doğrulayan açık istatistik platformu.</p>
                <hr class="my-4">
                <p>Her vatandaş kendi istatistik kurumunu kurabilir. Alternatif ekonomik göstergeler (enflasyon, maaş zammı vb.) oluşturun.</p>
                @guest
                <div class="mt-4">
                    <a class="btn btn-primary btn-lg" href="{{ route('register') }}" role="button">
                        <i class="fas fa-user-plus"></i> Hemen Kayıt Ol
                    </a>
                    <a class="btn btn-outline-primary btn-lg" href="{{ route('login') }}" role="button">
                        <i class="fas fa-sign-in-alt"></i> Giriş Yap
                    </a>
                </div>
                @endguest
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h4 class="card-title">Çoklu Rol Sistemi</h4>
                    <p class="card-text">
                        Admin, İstatistikçi ve Veri Sağlayıcı rolleri ile sistematik veri yönetimi.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calculator fa-3x text-success mb-3"></i>
                    <h4 class="card-title">DSL Hesaplama Motoru</h4>
                    <p class="card-text">
                        Basit dil ile hesaplama kuralları tanımlayın. <code>ortalama(deger)</code> gibi ifadeler kullanın.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                    <h4 class="card-title">Otomatik Veri Doğrulama</h4>
                    <p class="card-text">
                        Çoklu kaynaklardan gelen veriler otomatik olarak doğrulanır ve aykırı değerler tespit edilir.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Public Datasets -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Açık Veri Setleri</h3>
                    <div class="card-tools">
                        <a href="{{ route('api.datasets.index') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-code"></i> API
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $publicDatasets = \App\Models\Dataset::where('is_public', true)
                                ->withCount('dataPoints')
                                ->orderBy('created_at', 'desc')
                                ->take(6)
                                ->get();
                        @endphp
                        
                        @foreach($publicDatasets as $dataset)
                        <div class="col-md-4 mb-3">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $dataset->name }}</h3>
                                    <div class="card-tools">
                                        <span class="badge bg-info">{{ $dataset->data_points_count }} veri</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>{{ Str::limit($dataset->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Birim: {{ $dataset->unit }}</small>
                                        @auth
                                            @if(Auth::user()->role === 'provider')
                                                <a href="{{ route('provider.data-entry.create') }}?dataset={{ $dataset->id }}" 
                                                   class="btn btn-primary btn-sm">
                                                    Veri Ekle
                                                </a>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer text-center">
                    @auth
                        @if(Auth::user()->role === 'statistician')
                            <a href="{{ route('statistician.datasets.index') }}" class="btn btn-primary">
                                <i class="fas fa-database"></i> Tüm Veri Setlerini Gör
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
    
    <!-- API Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-code"></i> API Erişimi</h3>
                </div>
                <div class="card-body">
                    <p>Veri sağlayıcılar API üzerinden otomatik veri girişi yapabilirler.</p>
                    <div class="alert alert-light">
                        <code>POST {{ url('/api/data-points') }}</code>
                        <pre class="mt-2">{
    "dataset_id": 1,
    "date": "2024-01-27",
    "value": 45.50,
    "source_url": "https://example.com"
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
