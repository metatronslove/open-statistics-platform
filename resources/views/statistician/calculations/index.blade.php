@extends('layouts.app')

@section('title', 'Hesaplama Sonuçları')
@section('page_title', 'Hesaplama Sonuçları')

@section('breadcrumb')
    <li class="breadcrumb-item active">Hesaplamalar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('statistician.calculations.run-all') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" 
                        onclick="return confirm('Tüm hesaplama kurallarını çalıştırmak istediğinizden emin misiniz?')">
                    <i class="fas fa-calculator"></i> Tümünü Hesapla
                </button>
            </form>
            
            <a href="{{ route('statistician.rules.index') }}" class="btn btn-default float-right">
                <i class="fas fa-arrow-left"></i> Kurallara Dön
            </a>
        </div>
    </div>

    @if($calculations->isEmpty())
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calculator fa-4x text-muted mb-3"></i>
                        <h4>Henüz Hesaplama Yok</h4>
                        <p class="text-muted">
                            Hesaplama kuralı tanımlanmış veri setiniz bulunmuyor.
                            Önce bir veri seti oluşturun ve hesaplama kuralı ekleyin.
                        </p>
                        <a href="{{ route('statistician.datasets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Yeni Veri Seti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($calculations as $calculation)
            <div class="col-md-4 mb-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">{{ $calculation['dataset']->name }}</h3>
                        <div class="card-tools">
                            <span class="badge bg-info">{{ $calculation['dataset']->data_points_count }} veri</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if($calculation['result'] !== null)
                                <h2 class="display-4 text-success">
                                    {{ number_format($calculation['result'], 4) }}
                                    <small class="text-muted">{{ $calculation['dataset']->unit }}</small>
                                </h2>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Hesaplanamadı
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <strong>Hesaplama Kuralı:</strong>
                            <code class="d-block mt-1 p-2 bg-light rounded">
                                {{ $calculation['formula'] }}
                            </code>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Açıklama:</strong>
                            <p class="mb-0">{{ Str::limit($calculation['dataset']->description, 100) }}</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    {{ $calculation['dataset']->created_at->format('d.m.Y') }}
                                </small>
                            </div>
                            <div class="col-6 text-right">
                                <small class="text-muted">
                                    @if($calculation['dataset']->is_public)
                                        <span class="badge bg-success">Açık</span>
                                    @else
                                        <span class="badge bg-warning">Kapalı</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100">
                            <a href="{{ route('statistician.calculations.show', $calculation['dataset']) }}" 
                               class="btn btn-success">
                                <i class="fas fa-chart-line"></i> Detaylar
                            </a>
                            <a href="{{ route('statistician.datasets.show', $calculation['dataset']) }}" 
                               class="btn btn-outline-success">
                                <i class="fas fa-eye"></i> Veriler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary Stats -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Hesaplama Özeti</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Toplam Hesaplama</span>
                                        <span class="info-box-number">{{ count($calculations) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Başarılı</span>
                                        <span class="info-box-number">
                                            {{ $calculations->where('result', '!==', null)->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Başarısız</span>
                                        <span class="info-box-number">
                                            {{ $calculations->where('result', '===', null)->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-database"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Ort. Veri</span>
                                        <span class="info-box-number">
                                            {{ round($calculations->avg(function($c) { return $c['dataset']->data_points_count; }), 1) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
