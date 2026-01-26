@extends('layouts.app')

@section('title', 'Hesaplama Sonuçları')
@section('page_title', 'Tüm Hesaplama Sonuçları')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.rules.index') }}">Hesaplama Kuralları</a></li>
    <li class="breadcrumb-item active">Hesaplama Sonuçları</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hesaplama Sonuçları</h3>
                    <div class="card-tools">
                        <a href="{{ route('statistician.rules.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($results))
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Bilgi</h5>
                            <p>Henüz hesaplama kuralı tanımlanmış veri setiniz bulunmuyor.</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($results as $id => $result)
                            <div class="col-md-4 mb-3">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ $result['name'] }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <h2 class="display-4">
                                                {{ number_format($result['value'], 4) }}
                                                <small class="text-muted">{{ $result['unit'] }}</small>
                                            </h2>
                                            <p class="text-muted">
                                                <small>Hesaplama Kuralı:</small><br>
                                                <code>{{ $result['rule'] }}</code>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('statistician.datasets.show', $id) }}" 
                                           class="btn btn-success btn-block">
                                            <i class="fas fa-chart-line"></i> Detaylı Görüntüle
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
