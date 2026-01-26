@extends('layouts.app')

@section('title', 'Profilim')
@section('page_title', 'Profilim')

@section('breadcrumb')
    <li class="breadcrumb-item active">Profilim</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Sağlayıcı Profil Bilgileri</h3>
                </div>
                <form action="{{ route('provider.profile.update') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Önemli Bilgi</h5>
                            <p>
                                Profil bilgileriniz doğrulandıktan sonra sistemdeki güvenilirlik puanınız 
                                artacak ve verileriniz daha hızlı doğrulanacaktır.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="organization_name">Kurum/Kuruluş Adı *</label>
                            <input type="text" class="form-control @error('organization_name') is-invalid @enderror" 
                                   id="organization_name" name="organization_name" 
                                   value="{{ old('organization_name', $dataProvider->organization_name ?? '') }}" 
                                   required>
                            @error('organization_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Resmi kurum adınızı veya şahıs olarak çalışıyorsanız adınızı yazın.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="website">Web Sitesi</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" 
                                   value="{{ old('website', $dataProvider->website ?? '') }}"
                                   placeholder="https://ornek.com">
                            @error('website')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Web siteniz varsa ekleyin. Bu, doğrulanabilirlik için önemlidir.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $dataProvider->description ?? '') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Kurumunuz hakkında kısa bir açıklama yazın. Bu, kullanıcıların sizi 
                                tanımasına yardımcı olacaktır.
                            </small>
                        </div>

                        @if($dataProvider)
                        <div class="form-group">
                            <label>Mevcut Durum</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-star"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Güven Skoru</span>
                                            <span class="info-box-number">{{ $dataProvider->trust_score ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon 
                                            @if($dataProvider->is_verified) bg-success @else bg-warning @endif">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Doğrulama</span>
                                            <span class="info-box-number">
                                                @if($dataProvider->is_verified)
                                                    Doğrulanmış
                                                @else
                                                    Bekliyor
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Profili Kaydet
                        </button>
                        <a href="{{ route('provider.dashboard') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
