@extends('layouts.app')

@section('title', 'Veri Setlerim')
@section('page_title', 'Veri Setlerim')

@section('breadcrumb')
    <li class="breadcrumb-item active">Veri Setlerim</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('statistician.datasets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Veri Seti
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veri Setlerim</h3>
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
                                <th>İsim</th>
                                <th>Açıklama</th>
                                <th>Veri Noktası</th>
                                <th>Birim</th>
                                <th>Durum</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datasets as $dataset)
                            <tr>
                                <td>{{ $dataset->id }}</td>
                                <td>
                                    <a href="{{ route('statistician.datasets.show', $dataset) }}">
                                        {{ $dataset->name }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($dataset->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $dataset->data_points_count }}</span>
                                </td>
                                <td>{{ $dataset->unit }}</td>
                                <td>
                                    @if($dataset->is_public)
                                        <span class="badge bg-success">Açık</span>
                                    @else
                                        <span class="badge bg-warning">Kapalı</span>
                                    @endif
                                </td>
                                <td>{{ $dataset->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('statistician.datasets.show', $dataset) }}" 
                                           class="btn btn-sm btn-info" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" 
                                           class="btn btn-sm btn-warning" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('statistician.datasets.destroy', $dataset) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Bu veri setini silmek istediğinizden emin misiniz?')"
                                                    title="Sil">
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
                <div class="card-footer clearfix">
                    {{ $datasets->links() }}
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
