@extends('layouts.app')

@section('title', 'Yeni Hesaplama Kuralı')
@section('page_title', 'Yeni Hesaplama Kuralı Oluştur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.rules.index') }}">Hesaplama Kuralları</a></li>
    <li class="breadcrumb-item active">Yeni Oluştur</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Yeni Hesaplama Kuralı</h3>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Bilgi</h5>
                        <p>
                            Hesaplama kurallarını oluşturmak için önce bir veri seti oluşturmalı, 
                            ardından veri setinin düzenleme sayfasından hesaplama kuralını eklemelisiniz.
                        </p>
                        <a href="{{ route('statistician.datasets.create') }}" class="btn btn-info">
                            <i class="fas fa-plus"></i> Yeni Veri Seti Oluştur
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Mevcut Veri Setleriniz</h4>
                                </div>
                                <div class="card-body">
                                    @if($datasets->isEmpty())
                                        <div class="alert alert-warning">
                                            Henüz veri setiniz bulunmuyor. Önce bir veri seti oluşturun.
                                        </div>
                                    @else
                                        <div class="list-group">
                                            @foreach($datasets as $dataset)
                                                <a href="{{ route('statistician.datasets.edit', $dataset) }}" 
                                                   class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">{{ $dataset->name }}</h5>
                                                        <small>{{ $dataset->unit }}</small>
                                                    </div>
                                                    <p class="mb-1">{{ Str::limit($dataset->description, 100) }}</p>
                                                    <small>
                                                        @if($dataset->calculation_rule)
                                                            <span class="text-success">Kural tanımlı</span>
                                                        @else
                                                            <span class="text-warning">Kural tanımlanmamış</span>
                                                        @endif
                                                    </small>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Example Rules -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">Örnek Hesaplama Kuralları</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Açıklama</th>
                                                <th>DSL Kuralı</th>
                                                <th>Matematiksel İfade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($exampleRules as $description => $rule)
                                            <tr>
                                                <td>{{ $description }}</td>
                                                <td><code>{{ $rule }}</code></td>
                                                <td class="text-muted">
                                                    @if($description == 'Ortalama Hesaplama')
                                                        (Σx) / n
                                                    @elseif($description == 'Toplam ve Bölme')
                                                        Σx / n
                                                    @elseif($description == 'Maksimum ve Minimum Fark')
                                                        (max(x) - min(x)) / 2
                                                    @elseif($description == 'Standart Sapma Hesaplama')
                                                        √(Σ(x - μ)² / n)
                                                    @elseif($description == 'Değişim Oranı')
                                                        ((xₙ - x₁) / x₁) × 100
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DSL Test Area -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h4 class="card-title">Kural Test Alanı</h4>
                                </div>
                                <div class="card-body">
                                    <form id="testRuleForm">
                                        @csrf
                                        <div class="form-group">
                                            <label for="test_dataset_id">Test Edilecek Veri Seti</label>
                                            <select class="form-control" id="test_dataset_id" required>
                                                <option value="">Seçiniz</option>
                                                @foreach($datasets as $dataset)
                                                    <option value="{{ $dataset->id }}">
                                                        {{ $dataset->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="test_rule">Test Kuralı</label>
                                            <textarea class="form-control" id="test_rule" rows="3" 
                                                      placeholder="ortalama(deger)"></textarea>
                                        </div>
                                        
                                        <button type="button" class="btn btn-warning" id="testRuleBtn">
                                            <i class="fas fa-play"></i> Kuralı Test Et
                                        </button>
                                        
                                        <div id="testResult" class="mt-3" style="display: none;">
                                            <div class="alert alert-success">
                                                <h5><i class="fas fa-check"></i> Test Sonucu</h5>
                                                <p id="resultText"></p>
                                            </div>
                                        </div>
                                    </form>
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#testRuleBtn').click(function() {
            var datasetId = $('#test_dataset_id').val();
            var rule = $('#test_rule').val();
            
            if (!datasetId || !rule) {
                alert('Lütfen veri seti ve kural girin.');
                return;
            }
            
            $.ajax({
                url: '{{ route("statistician.rules.test") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dataset_id: datasetId,
                    rule: rule
                },
                success: function(response) {
                    if (response.success) {
                        $('#resultText').text('Sonuç: ' + (response.result ? response.result.toFixed(4) : 'Hesaplanamadı'));
                        $('#testResult').show();
                    }
                },
                error: function(xhr) {
                    alert('Test sırasında bir hata oluştu.');
                }
            });
        });
    });
</script>
@endpush
