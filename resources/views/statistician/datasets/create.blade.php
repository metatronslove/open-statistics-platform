@extends('layouts.app')

@section('title', 'Yeni Veri Seti')
@section('page_title', 'Yeni Veri Seti Oluştur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('statistician.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item active">Yeni Veri Seti</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
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
                                   id="unit" name="unit" value="{{ old('unit') }}" required 
                                   placeholder="Örn: TL, USD, Adet, %">
                            @error('unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="calculation_rule">Hesaplama Kuralı (DSL)</label>
                            <textarea class="form-control @error('calculation_rule') is-invalid @enderror" 
                                      id="calculation_rule" name="calculation_rule" rows="4" 
                                      placeholder="Örn: ortalama(deger), topla(deger) / sayi">{{ old('calculation_rule') }}</textarea>
                            @error('calculation_rule')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Kullanılabilir fonksiyonlar: ortalama(deger), topla(deger), max(deger), min(deger), sayi
                            </small>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" 
                                   id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : 'checked' }}>
                            <label class="form-check-label" for="is_public">Herkes görebilsin</label>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Veri Seti Oluştur</button>
                        <a href="{{ route('statistician.datasets.index') }}" class="btn btn-default">İptal</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">DSL Örnekleri</h3>
                </div>
                <div class="card-body">
                    <h5>Basit Fonksiyonlar:</h5>
                    <ul>
                        <li><code>ortalama(deger)</code> - Ortalama hesaplama</li>
                        <li><code>topla(deger)</code> - Toplam hesaplama</li>
                        <li><code>max(deger)</code> - Maksimum değer</li>
                        <li><code>min(deger)</code> - Minimum değer</li>
                        <li><code>sayi</code> - Veri noktası sayısı</li>
                    </ul>
                    
                    <h5>Kompleks İfadeler:</h5>
                    <ul>
                        <li><code>topla(deger) / sayi</code> - Ortalama (alternatif)</li>
                        <li><code>(max(deger) - min(deger)) / 2</code> - Ortalama fark</li>
                        <li><code>(ortalama(deger) * 1.18) - 5</code> - Formül uygulama</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Not:</strong> Hesaplama sadece doğrulanmış veri noktaları üzerinden yapılır.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
