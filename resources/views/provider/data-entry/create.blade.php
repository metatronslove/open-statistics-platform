@extends('layouts.app')

@section('title', 'Yeni Veri Girişi')
@section('page_title', 'Yeni Veri Girişi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('provider.data-entry.index') }}">Verilerim</a></li>
    <li class="breadcrumb-item active">Yeni Veri Gir</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Yeni Veri Girişi Formu</h3>
                </div>
                <form action="{{ route('provider.data-entry.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Önemli Notlar</h5>
                            <ul class="mb-0">
                                <li>Lütfen doğru ve güvenilir veriler girin.</li>
                                <li>Kaynak URL'si verirseniz doğrulanma süreciniz hızlanır.</li>
                                <li>Aynı tarih ve veri seti için sadece bir veri girebilirsiniz.</li>
                                <li>Verileriniz sistemde otomatik doğrulanacaktır.</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label for="dataset_id">Veri Seti *</label>
                            <select class="form-control @error('dataset_id') is-invalid @enderror" 
                                    id="dataset_id" name="dataset_id" required>
                                <option value="">Seçiniz</option>
                                @foreach($datasets as $dataset)
                                    <option value="{{ $dataset->id }}" 
                                            {{ old('dataset_id', request('dataset_id')) == $dataset->id ? 'selected' : '' }}>
                                        {{ $dataset->name }} ({{ $dataset->unit }})
                                        @if($dataset->description)
                                            - {{ Str::limit($dataset->description, 50) }}
                                        @endif
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
                                   id="date" name="date" 
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   required>
                            @error('date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Geçmiş tarihli veri girişi yapabilirsiniz.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="value">Değer *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" 
                                       value="{{ old('value') }}" 
                                       step="0.0001" 
                                       min="0" 
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <span id="unitDisplay">TL</span>
                                    </span>
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
                                   value="{{ old('source_url') }}"
                                   placeholder="https://ornek.com/veri-kaynagi">
                            @error('source_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Verinizi doğrulayan bir web sayfası linki (isteğe bağlı).
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notlar (Opsiyonel)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Veri hakkında ek açıklamalar, ölçüm koşulları vb.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Veri Sağlayıcı Bilgileri</label>
                            <div class="alert alert-light">
                                <strong>Kurum:</strong> {{ $dataProvider->organization_name }}<br>
                                <strong>Güven Skoru:</strong> {{ $dataProvider->trust_score }}<br>
                                <strong>Doğrulama Durumu:</strong> 
                                @if($dataProvider->is_verified)
                                    <span class="badge bg-success">Doğrulanmış</span>
                                @else
                                    <span class="badge bg-warning">Bekliyor</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Veriyi Kaydet
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Dataset seçimine göre birimi güncelle
        $('#dataset_id').change(function() {
            var datasetId = $(this).val();
            var datasets = @json($datasets->keyBy('id'));
            
            if (datasetId && datasets[datasetId]) {
                $('#unitDisplay').text(datasets[datasetId].unit);
            } else {
                $('#unitDisplay').text('TL');
            }
        });

        // Sayfa yüklendiğinde birimi güncelle
        var initialDatasetId = $('#dataset_id').val();
        if (initialDatasetId) {
            $('#dataset_id').trigger('change');
        }
    });
</script>
@endpush
