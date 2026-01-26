@extends('layouts.app')

@section('title', 'Hesaplama Kuralları')
@section('page_title', 'Hesaplama Kuralları')

@section('breadcrumb')
    <li class="breadcrumb-item active">Hesaplama Kuralları</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('statistician.rules.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Kural Oluştur
            </a>
            <a href="{{ route('statistician.rules.calculate') }}" class="btn btn-success float-right">
                <i class="fas fa-calculator"></i> Tüm Kuralları Çalıştır
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hesaplama Kurallarım</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Hesaplama Kuralı</th>
                                <th>Birim</th>
                                <th>Veri Noktası</th>
                                <th>Sonuç</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datasets as $dataset)
                            <tr>
                                <td>
                                    <a href="{{ route('statistician.datasets.show', $dataset) }}">
                                        {{ $dataset->name }}
                                    </a>
                                </td>
                                <td>
                                    <code>{{ $dataset->calculation_rule }}</code>
                                </td>
                                <td>{{ $dataset->unit }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $dataset->data_points_count }}</span>
                                </td>
                                <td>
                                    @if(isset($results[$dataset->id]))
                                        <strong class="text-success">
                                            {{ number_format($results[$dataset->id], 4) }}
                                        </strong>
                                    @else
                                        <span class="text-muted">Hesaplanamadı</span>
                                    @endif
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
                            @if($datasets->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="alert alert-info m-0">
                                        Henüz hesaplama kuralı tanımlanmış veri setiniz bulunmuyor.
                                        <br>
                                        <a href="{{ route('statistician.rules.create') }}" class="btn btn-info btn-sm mt-2">
                                            İlk Kuralınızı Oluşturun
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $datasets->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- DSL Examples -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">DSL Örnekleri</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Temel İşlemler</h4>
                                </div>
                                <div class="card-body">
                                    <pre><code>ortalama(deger)
topla(deger)
max(deger)
min(deger)
sayi</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Matematiksel İşlemler</h4>
                                </div>
                                <div class="card-body">
                                    <pre><code>topla(deger) / sayi
(max(deger) - min(deger)) / 2
ortalama(deger) * 1.18
topla(deger) * 0.01</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Kompleks Kurallar</h4>
                                </div>
                                <div class="card-body">
                                    <pre><code>((max(deger) - min(deger)) / 
ortalama(deger)) * 100
(topla(deger) - min(deger)) / 
(sayi - 1)
(ortalama(deger) / 
max(deger)) * 100</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
