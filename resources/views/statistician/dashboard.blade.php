@extends('layouts.app')

@section('title', 'İstatistikçi Dashboard')
@section('page_title', 'İstatistikçi Paneli')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Hızlı İstatistikler -->
    <div class="row">
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
                    Tümünü Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ count($calculatedValues) }}</h3>
                    <p>Hesaplanan Değer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <a href="{{ route('statistician.rules.index') }}" class="small-box-footer">
                    Hesaplamalar <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingValidations->count() }}</h3>
                    <p>Doğrulama Bekleyen</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="#" class="small-box-footer">
                    İncele <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $recentDataPoints->count() }}</h3>
                    <p>Son Veri Noktaları</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Detaylar <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Hesaplanan Değerler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hesaplanan Değerler</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Hesaplanan Değer</th>
                                <th>Birim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculatedValues as $id => $value)
                            <tr>
                                <td>{{ $value['name'] }}</td>
                                <td>
                                    <strong>{{ number_format($value['value'], 4) }}</strong>
                                </td>
                                <td>{{ $value['unit'] }}</td>
                            </tr>
                            @endforeach
                            @if(empty($calculatedValues))
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="alert alert-info m-0">
                                        Henüz hesaplanan değer bulunmuyor.
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Doğrulama Bekleyen Veriler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Doğrulama Bekleyen Veriler</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Tarih</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingValidations as $validation)
                            <tr>
                                <td>{{ $validation->dataset->name }}</td>
                                <td>{{ $validation->date->format('d.m.Y') }}</td>
                                <td>
                                    <span class="badge bg-warning">Bekliyor</span>
                                </td>
                            </tr>
                            @endforeach
                            @if($pendingValidations->isEmpty())
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="alert alert-success m-0">
                                        Doğrulama bekleyen veri bulunmuyor.
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Veri Setlerim -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Veri Setlerim</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>İsim</th>
                                <th>Açıklama</th>
                                <th>Veri Noktası</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myDatasets as $dataset)
                            <tr>
                                <td>
                                    <a href="{{ route('statistician.datasets.show', $dataset) }}">
                                        {{ $dataset->name }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($dataset->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $dataset->data_points_count }}</span>
                                </td>
                                <td>{{ $dataset->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('statistician.datasets.show', $dataset) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" 
                                           class="btn btn-sm btn-warning">
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
                    <a href="{{ route('statistician.datasets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Veri Seti
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Veri Noktaları -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Veri Noktaları</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
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
                                <td>{{ $dataPoint->dataProvider->organization_name }}</td>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
