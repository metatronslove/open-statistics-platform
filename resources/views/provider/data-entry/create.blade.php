@extends('layouts.app')

@section('title', 'Yeni Veri Ekle')
@section('page_title', 'Yeni Veri Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('provider.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('provider.data-entry.index') }}">Veri Girişleri</a></li>
    <li class="breadcrumb-item active">Yeni Veri</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$dataProvider)
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Erişim Engellendi</h5>
            Veri girişi yapabilmek için önce veri sağlayıcı profilinizi tamamlamanız gerekiyor.
            <a href="{{ route('provider.profile') }}" class="btn btn-danger btn-sm ml-3">Profili Tamamla</a>
        </div>
        @endsection
        @php return; @endphp
    @endif
    
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Bilgileri</h3>
                </div>
                <form action="{{ route('provider.data-entry.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="dataset_id">Veri Seti *</label>
                            <select class="form-control @error('dataset_id') is-invalid @enderror" 
                                    id="dataset_id" name="dataset_id" required>
                                <option value="">Seçiniz</option>
                                @foreach($datasets as $dataset)
                                <option value="{{ $dataset->id }}" 
                                        {{ old('dataset_id', request('dataset')) == $dataset->id ? 'selected' : '' }}>
                                    {{ $dataset->name }} ({{ $dataset->unit }})
                                </option>
                                @endforeach
                            </select>
                            @error('dataset_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="date">Tarih *</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                   id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}" required>
                            @error('date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Gelecek tarihli veri giremezsiniz. Geçmiş tarihler serbest.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="value">Değer *</label>
                            <div class="input-group">
                                <input type="number" step="0.0001" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" value="{{ old('value') }}" required 
                                       min="0" max="999999999.9999">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="unit_display">-</span>
                                </div>
                            </div>
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="source_url">Kaynak URL (Opsiyonel)</label>
                            <input type="url" class="form-control @error('source_url') is-invalid @enderror" 
                                   id="source_url" name="source_url" value="{{ old('source_url') }}" 
                                   placeholder="https://example.com/veri-kaynagi">
                            @error('source_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Veriyi doğrulamak için kaynak linki ekleyebilirsiniz.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notlar (Opsiyonel)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Veri hakkında ek bilgiler...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Veriyi Kaydet
                        </button>
                        <a href="{{ route('provider.data-entry.index') }}" class="btn btn-default">İptal</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Provider Info -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Veri Sağlayıcı Bilgileri</h3>
                </div>
                <div class="card-body">
                    <p><strong>Kurum:</strong> {{ $dataProvider->organization_name }}</p>
                    <p><strong>Doğrulama:</strong> 
                        @if($dataProvider->is_verified)
                            <span class="badge bg-success">Doğrulanmış</span>
                        @else
                            <span class="badge bg-warning">Bekliyor</span>
                        @endif
                    </p>
                    <p><strong>Güven Skoru:</strong> 
                        <span class="badge bg-{{ $dataProvider->trust_score >= 80 ? 'success' : ($dataProvider->trust_score >= 60 ? 'warning' : 'danger') }}">
                            {{ number_format($dataProvider->trust_score, 1) }}
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Duplicate Warning -->
            <div class="card card-warning mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Dikkat</h3>
                </div>
                <div class="card-body">
                    <p>Aynı tarih için aynı veri setine sadece <strong>bir kez</strong> veri girebilirsiniz.</p>
                    <p>Eğer aynı tarih için veri girmişseniz, lütfen eski veriyi düzenleyin.</p>
                    <div class="alert alert-light">
                        <small>
                            <strong>Veri Doğrulama Süreci:</strong><br>
                            1. Veriniz kaydedilir<br>
                            2. Aynı tarihte 2+ veri olunca otomatik doğrulama başlar<br>
                            3. Sistem ortalamayı hesaplar ve aykırı değerleri işaretler<br>
                            4. Doğrulama sonucu size bildirilir
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Recent Entries -->
            <div class="card card-primary mt-3">
                <div class="card-header">
                    <h3 class="card-title">Son Veri Girişleriniz</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @php
                            $recentEntries = $dataProvider->dataPoints()
                                ->with('dataset')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        @foreach($recentEntries as $entry)
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                {{ $entry->dataset->name }} 
                                <span class="float-right text-muted">
                                    {{ $entry->date->format('d.m') }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Dataset unit display
        function updateUnitDisplay() {
            const datasetId = $('#dataset_id').val();
            const datasets = @json($datasets->keyBy('id'));
            
            if (datasetId && datasets[datasetId]) {
                $('#unit_display').text(datasets[datasetId].unit);
            } else {
                $('#unit_display').text('-');
            }
        }
        
        $('#dataset_id').change(updateUnitDisplay);
        updateUnitDisplay(); // Initial call
        
        // Form validation
        $('form').submit(function(e) {
            const value = parseFloat($('#value').val());
            if (value < 0) {
                alert('Değer sıfırdan küçük olamaz!');
                e.preventDefault();
                return false;
            }
            
            if (value > 999999999.9999) {
                alert('Değer çok büyük!');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    });
</script>
@endpush
