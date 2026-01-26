@extends('layouts.app')

@section('title', 'Doğrulama Geçmişi')
@section('page_title', 'Doğrulama Geçmişi')

@section('breadcrumb')
    <li class="breadcrumb-item active">Doğrulamalar</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-history"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Toplam Doğrulama</span>
                    <span class="info-box-number">{{ $validationLogs->total() }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Doğrulanmış</span>
                    <span class="info-box-number">{{ $statusStats['verified'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bekleyen</span>
                    <span class="info-box-number">{{ $statusStats['pending'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Başarısız</span>
                    <span class="info-box-number">{{ $statusStats['failed'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Doğrulama Kayıtları</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Ara...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Veri Seti</th>
                                <th>Tarih</th>
                                <th>Ortalama</th>
                                <th>Toplam Veri</th>
                                <th>Geçerli Veri</th>
                                <th>Durum</th>
                                <th>İşlem Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validationLogs as $validation)
                            <tr>
                                <td>{{ $validation->id }}</td>
                                <td>
                                    <a href="{{ route('admin.datasets.show', $validation->dataset) }}">
                                        {{ $validation->dataset->name }}
                                    </a>
                                </td>
                                <td>{{ $validation->date->format('d.m.Y') }}</td>
                                <td>
                                    @if($validation->calculated_average)
                                        {{ number_format($validation->calculated_average, 4) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $validation->total_points }}</td>
                                <td>
                                    <span class="{{ $validation->valid_points == $validation->total_points ? 'text-success' : 'text-warning' }}">
                                        {{ $validation->valid_points }}
                                    </span>
                                </td>
                                <td>
                                    @if($validation->status == 'verified')
                                        <span class="badge bg-success">Doğrulandı</span>
                                    @elseif($validation->status == 'failed')
                                        <span class="badge bg-danger">Başarısız</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>{{ $validation->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.validations.show', $validation) }}" 
                                           class="btn btn-sm btn-info" title="Detaylar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($validation->status != 'verified')
                                        <form action="{{ route('admin.validations.retry', $validation) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" 
                                                    onclick="return confirm('Bu doğrulama işlemini tekrar başlatmak istediğinizden emin misiniz?')"
                                                    title="Tekrar Dene">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $validationLogs->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Outliers -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Son Aykırı Değerler</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Veri Seti</th>
                                    <th>Tarih</th>
                                    <th>Sağlayıcı</th>
                                    <th>Girilen Değer</th>
                                    <th>Ortalama</th>
                                    <th>Standart Sapma</th>
                                    <th>Fark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentOutliers = [];
                                    foreach($validationLogs as $validation) {
                                        if ($validation->outliers && $validation->status != 'verified') {
                                            foreach(json_decode($validation->outliers, true) as $outlier) {
                                                $recentOutliers[] = [
                                                    'dataset' => $validation->dataset->name,
                                                    'date' => $validation->date->format('d.m.Y'),
                                                    'provider' => $outlier['provider'] ?? 'Bilinmiyor',
                                                    'value' => $outlier['value'] ?? 0,
                                                    'average' => $validation->calculated_average,
                                                    'stddev' => $validation->standard_deviation,
                                                ];
                                            }
                                        }
                                    }
                                    $recentOutliers = array_slice($recentOutliers, 0, 10);
                                @endphp
                                
                                @if(count($recentOutliers) > 0)
                                    @foreach($recentOutliers as $outlier)
                                    <tr>
                                        <td>{{ $outlier['dataset'] }}</td>
                                        <td>{{ $outlier['date'] }}</td>
                                        <td>{{ $outlier['provider'] }}</td>
                                        <td>{{ number_format($outlier['value'], 4) }}</td>
                                        <td>{{ number_format($outlier['average'], 4) }}</td>
                                        <td>{{ number_format($outlier['stddev'], 4) }}</td>
                                        <td>
                                            @php
                                                $diff = abs($outlier['value'] - $outlier['average']);
                                                $diffPercent = $outlier['average'] > 0 ? ($diff / $outlier['average']) * 100 : 0;
                                            @endphp
                                            <span class="text-danger">
                                                %{{ number_format($diffPercent, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-success m-0">
                                                Son 24 saatte aykırı değer bulunamadı.
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
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('input[name="table_search"]').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endpush
