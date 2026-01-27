@extends('layouts.app')

@section('title', 'Veri Girişlerim')
@section('page_title', 'Veri Girişlerim')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('provider.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Veri Girişleri</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$dataProvider)
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Erişim Engellendi</h5>
            Veri girişi yapabilmek için önce veri sağlayıcı profilinizi tamamlamanız gerekiyor.
            <a href="{{ route('provider.profile') }}" class="btn btn-danger btn-sm ml-3">Profili Tamamla</a>
        </div>
        @endsection
        @php return; @endphp
    @endif
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veri Girişlerim</h3>
                    <div class="card-tools">
                        <a href="{{ route('provider.data-entry.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Veri Ekle
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Veri Seti</th>
                                <th>Tarih</th>
                                <th>Girilen Değer</th>
                                <th>Doğrulanmış Değer</th>
                                <th>Durum</th>
                                <th>Kaynak</th>
                                <th>Notlar</th>
                                <th>İşlem Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myDataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->id }}</td>
                                <td>{{ $dataPoint->dataset->name }}</td>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>{{ number_format($dataPoint->value, 4) }} {{ $dataPoint->dataset->unit }}</td>
                                <td>
                                    @if($dataPoint->verified_value)
                                        {{ number_format($dataPoint->verified_value, 4) }} {{ $dataPoint->dataset->unit }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->source_url)
                                        <a href="{{ $dataPoint->source_url }}" target="_blank" class="btn btn-xs btn-info">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ Str::limit($dataPoint->notes, 20) }}</td>
                                <td>{{ $dataPoint->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('provider.data-entry.edit', $dataPoint) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('provider.data-entry.destroy', $dataPoint) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu veriyi silmek istediğinize emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $myDataPoints->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mt-3">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Toplam Veri</span>
                    <span class="info-box-number">{{ $myDataPoints->total() }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Doğrulanmış</span>
                    <span class="info-box-number">
                        {{ $dataProvider->dataPoints()->where('is_verified', true)->count() }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bekleyen</span>
                    <span class="info-box-number">
                        {{ $dataProvider->dataPoints()->where('is_verified', false)->count() }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Doğrulama Oranı</span>
                    <span class="info-box-number">
                        @php
                            $total = $dataProvider->dataPoints()->count();
                            $verified = $dataProvider->dataPoints()->where('is_verified', true)->count();
                            $rate = $total > 0 ? round(($verified / $total) * 100, 1) : 0;
                        @endphp
                        {{ $rate }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
