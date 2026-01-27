@extends('layouts.app')

@section('title', 'Veri Setlerim')
@section('page_title', 'Veri Setlerim')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Veri Setleri</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veri Setleri</h3>
                    <div class="card-tools">
                        <a href="{{ route('statistician.datasets.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Veri Seti
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İsim</th>
                                <th>Açıklama</th>
                                <th>Birim</th>
                                <th>Veri Noktası</th>
                                <th>Doğrulama</th>
                                <th>Durum</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datasets as $dataset)
                            <tr>
                                <td>{{ $dataset->id }}</td>
                                <td>{{ $dataset->name }}</td>
                                <td>{{ Str::limit($dataset->description, 50) }}</td>
                                <td>{{ $dataset->unit }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $dataset->data_points_count }}</span>
                                </td>
                                <td>
                                    @if($dataset->calculation_rule)
                                        <span class="badge bg-success">Kural Var</span>
                                    @else
                                        <span class="badge bg-secondary">Kural Yok</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $dataset->is_public ? 'success' : 'secondary' }}">
                                        {{ $dataset->is_public ? 'Açık' : 'Kapalı' }}
                                    </span>
                                </td>
                                <td>{{ $dataset->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('statistician.datasets.show', $dataset) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('statistician.datasets.destroy', $dataset) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu veri setini silmek istediğinize emin misiniz?')">
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
                    {{ $datasets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
