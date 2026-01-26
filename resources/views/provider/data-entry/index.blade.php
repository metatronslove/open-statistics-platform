@extends('layouts.app')

@section('title', 'Verilerim')
@section('page_title', 'Verilerim')

@section('breadcrumb')
    <li class="breadcrumb-item active">Verilerim</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('provider.data-entry.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Veri Gir
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
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
                        {{ $myDataPoints->where('is_verified', true)->count() }}
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
                        {{ $myDataPoints->where('is_verified', false)->count() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kurum</span>
                    <span class="info-box-number">{{ $dataProvider->organization_name }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Verilerim</h3>
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
                                <th>Değer</th>
                                <th>Doğrulanmış Değer</th>
                                <th>Kaynak</th>
                                <th>Durum</th>
                                <th>Eklenme</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myDataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->id }}</td>
                                <td>{{ $dataPoint->dataset->name }}</td>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>
                                    <strong>{{ number_format($dataPoint->value, 4) }}</strong>
                                    {{ $dataPoint->dataset->unit }}
                                </td>
                                <td>
                                    @if($dataPoint->verified_value)
                                        <strong class="text-success">
                                            {{ number_format($dataPoint->verified_value, 4) }}
                                        </strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->source_url)
                                        <a href="{{ $dataPoint->source_url }}" target="_blank" 
                                           class="btn btn-xs btn-info" title="Kaynağı Gör">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulandı</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>{{ $dataPoint->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('provider.data-entry.edit', $dataPoint) }}" 
                                           class="btn btn-sm btn-warning" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('provider.data-entry.destroy', $dataPoint) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Bu veriyi silmek istediğinizden emin misiniz?')"
                                                    title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if($myDataPoints->isEmpty())
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="alert alert-info m-0">
                                        <h5><i class="fas fa-info-circle"></i> Henüz veri girişi yapmadınız</h5>
                                        <p class="mb-0">İlk verinizi girmek için "Yeni Veri Gir" butonunu kullanın.</p>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $myDataPoints->links() }}
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
