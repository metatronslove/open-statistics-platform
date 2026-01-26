@extends('layouts.app')

@section('title', 'Veri Düzenle')
@section('page_title', 'Veri Düzenle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('provider.data-entry.index') }}">Verilerim</a></li>
    <li class="breadcrumb-item active">Veri Düzenle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Veri Düzenle</h3>
                </div>
                <form action="{{ route('provider.data-entry.update', $dataPoint) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Dikkat</h5>
                            <ul class="mb-0">
                                <li>Veriyi güncellediğinizde doğrulama durumu sıfırlanacaktır.</li>
                                <li>Sadece değer, kaynak URL ve notları değiştirebilirsiniz.</li>
                                <li>Veri seti ve tarih değiştirilemez.</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label>Veri Seti</label>
                            <input type="text" class="form-control" value="{{ $dataPoint->dataset->name }} ({{ $dataPoint->dataset->unit }})" readonly>
                        </div>

                        <div class="form-group">
                            <label>Tarih</label>
                            <input type="text" class="form-control" value="{{ $dataPoint->date->format('d.m.Y') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="value">Değer *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" 
                                       value="{{ old('value', $dataPoint->value) }}" 
                                       step="0.0001" 
                                       min="0" 
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ $dataPoint->dataset->unit }}</span>
                                </div>
                            </div>
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="source_url">Kaynak URL (Opsiyonel)</label>
                            <input type="url" class="form-control @error('source_url') is-invalid @enderror" 
                                   id="source_url" name="source_url" 
                                   value="{{ old('source_url', $dataPoint->source_url) }}"
                                   placeholder="https://ornek.com/veri-kaynagi">
                            @error('source_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notlar (Opsiyonel)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $dataPoint->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Mevcut Durum</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon 
                                            @if($dataPoint->is_verified) bg-success @else bg-warning @endif">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Doğrulama</span>
                                            <span class="info-box-number">
                                                @if($dataPoint->is_verified)
                                                    Doğrulanmış
                                                @else
                                                    Bekliyor
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-history"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Son Güncelleme</span>
                                            <span class="info-box-number">
                                                {{ $dataPoint->updated_at->format('d.m.Y H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('provider.data-entry.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
