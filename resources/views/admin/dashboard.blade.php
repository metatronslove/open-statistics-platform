@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Yönetici Paneli')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Sistem İstatistikleri -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p>Toplam Kullanıcı</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                    Detaylı Görüntüle <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_datasets'] }}</h3>
                    <p>Veri Seti</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
                <a href="{{ route('admin.datasets.index') }}" class="small-box-footer">
                    Detaylı Görüntüle <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_data_points'] }}</h3>
                    <p>Veri Noktası</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Sistem İstatistikleri <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['verified_providers'] }}/{{ $stats['total_providers'] }}</h3>
                    <p>Doğrulanmış Sağlayıcı</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                    Yönet <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Son Kullanıcılar -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Kayıt Olan Kullanıcılar</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>İsim</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Kayıt Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge 
                                        @if($user->role == 'admin') bg-danger
                                        @elseif($user->role == 'statistician') bg-warning
                                        @else bg-info @endif">
                                        {{ $user->role == 'admin' ? 'Yönetici' : 
                                           ($user->role == 'statistician' ? 'İstatistikçi' : 'Sağlayıcı') }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Son Veri Setleri -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Oluşturulan Veri Setleri</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>İsim</th>
                                <th>Oluşturan</th>
                                <th>Birim</th>
                                <th>Oluşturulma</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDatasets as $dataset)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.datasets.show', $dataset) }}">
                                        {{ $dataset->name }}
                                    </a>
                                </td>
                                <td>{{ $dataset->creator->name }}</td>
                                <td>{{ $dataset->unit }}</td>
                                <td>{{ $dataset->created_at->format('d.m.Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Doğrulama İstatistikleri -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Doğrulama İstatistikleri</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doğrulanmış Veriler</span>
                                    <span class="info-box-number">{{ $stats['verified_data_points'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doğrulama Bekleyen</span>
                                    <span class="info-box-number">{{ $stats['pending_verifications'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doğrulama Oranı</span>
                                    <span class="info-box-number">
                                        @if($stats['total_data_points'] > 0)
                                            {{ round(($stats['verified_data_points'] / $stats['total_data_points']) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-chart-bar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Veri</span>
                                    <span class="info-box-number">{{ $stats['total_data_points'] }}</span>
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
