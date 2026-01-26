@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi')
@section('page_title', 'Kullanıcı Yönetimi')

@section('breadcrumb')
    <li class="breadcrumb-item active">Kullanıcılar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Kullanıcı
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Kullanıcılar</h3>
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
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Kayıt Tarihi</th>
                                <th>Son Giriş</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" 
                                             class="img-circle mr-2" width="30" height="30" alt="{{ $user->name }}">
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger">Yönetici</span>
                                    @elseif($user->role == 'statistician')
                                        <span class="badge bg-warning">İstatistikçi</span>
                                    @else
                                        <span class="badge bg-info">Veri Sağlayıcı</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('d.m.Y H:i') }}
                                    @else
                                        <span class="text-muted">Henüz giriş yapmadı</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-sm btn-warning" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')"
                                                    title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Veri Sağlayıcı Detayları -->
                            @if($user->dataProvider)
                            <tr class="bg-light">
                                <td colspan="8">
                                    <div class="pl-5">
                                        <small>
                                            <strong>Kurum:</strong> {{ $user->dataProvider->organization_name }}
                                            @if($user->dataProvider->website)
                                                | <strong>Web:</strong> 
                                                <a href="{{ $user->dataProvider->website }}" target="_blank">
                                                    {{ $user->dataProvider->website }}
                                                </a>
                                            @endif
                                            | <strong>Güven Skoru:</strong> {{ $user->dataProvider->trust_score }}
                                            | <strong>Doğrulama:</strong>
                                            @if($user->dataProvider->is_verified)
                                                <span class="badge bg-success btn-sm">Doğrulanmış</span>
                                            @else
                                                <span class="badge bg-warning btn-sm">Bekliyor</span>
                                                <form action="{{ route('admin.providers.verify', $user->dataProvider) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-success">
                                                        Doğrula
                                                    </button>
                                                </form>
                                            @endif
                                        </small>
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $users->links() }}
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
