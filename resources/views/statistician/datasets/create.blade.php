@extends('layouts.app')

@section('title', 'Yeni Veri Seti')
@section('page_title', 'Yeni Veri Seti Oluştur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.datasets.index') }}">Veri Setlerim</a></li>
    <li class="breadcrumb-item active">Yeni Oluştur</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                </div>
                <form action="{{ route('statistician.datasets.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Veri Seti Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unit">Birim *</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                   id="unit" name="unit" value="{{ old('unit', 'TL') }}" required>
                            @error('unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Örnek: TL, USD, Adet, Litre vb.</small>
                        </div>

                        <div class="form-group">
                            <label for="calculation_rule">Hesaplama Kuralı (DSL)</label>
                            <textarea class="form-control @error('calculation_rule') is-invalid @enderror" 
                                      id="calculation_rule" name="calculation_rule" rows="4" 
                                      placeholder="Örnek: ortalama(deger), topla(deger) / sayi">{{ old('calculation_rule') }}</textarea>
                            @error('calculation_rule')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Kullanılabilir fonksiyonlar: ortalama(), topla(), max(), min(), sayi<br>
                                Örnek kurallar:<br>
                                • ortalama(deger) - Tüm değerlerin ortalaması<br>
                                • topla(deger) / sayi - Toplamın sayıya bölümü<br>
                                • (max(deger) - min(deger)) / 2 - Maksimum ve minimum farkının yarısı
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="is_public" name="is_public" value="1" checked>
                                <label class="custom-control-label" for="is_public">
                                    Veri seti herkese açık olsun
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Açık veri setlerine tüm veri sağlayıcılar veri girebilir.
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="{{ route('statistician.datasets.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
