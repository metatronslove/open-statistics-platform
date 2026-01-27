@extends('layouts.app')

@section('title', 'Hesaplama Kuralları')
@section('page_title', 'Hesaplama Kuralları')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Hesaplama Kuralları</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Hesaplama Kuralları</h3>
                    <div class="card-tools">
                        <a href="{{ route('statistician.rules.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Kural
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#testRuleModal">
                            <i class="fas fa-vial"></i> Kural Test Et
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Hesaplama Kuralı</th>
                                <th>Sonuç</th>
                                <th>Birim</th>
                                <th>Durum</th>
                                <th>Veri Noktası</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datasets as $dataset)
                            <tr>
                                <td>
                                    <a href="{{ route('statistician.datasets.show', $dataset) }}">
                                        {{ $dataset->name }}
                                    </a>
                                </td>
                                <td>
                                    <code>{{ $dataset->calculation_rule }}</code>
                                </td>
                                <td>
                                    @if(isset($results[$dataset->id]) && $results[$dataset->id] !== null)
                                        <strong>{{ number_format($results[$dataset->id], 4) }}</strong>
                                    @else
                                        <span class="text-muted">Hesaplanamadı</span>
                                    @endif
                                </td>
                                <td>{{ $dataset->unit }}</td>
                                <td>
                                    @if($dataset->calculation_rule)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $dataset->data_points_count }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $datasets->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- DSL Info Card -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-code"></i> DSL (Domain-Specific Language) Rehberi</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Kullanılabilir Fonksiyonlar:</h5>
                            <ul>
                                <li><code>ortalama(deger)</code> veya <code>mean(deger)</code> - Ortalama hesaplama</li>
                                <li><code>topla(deger)</code> - Toplam hesaplama</li>
                                <li><code>max(deger)</code> - Maksimum değer</li>
                                <li><code>min(deger)</code> - Minimum değer</li>
                                <li><code>sayi</code> - Veri noktası sayısı</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Örnek Kurallar:</h5>
                            <ul>
                                <li><code>ortalama(deger)</code> - Basit ortalama</li>
                                <li><code>topla(deger) / sayi</code> - Ortalama (alternatif)</li>
                                <li><code>(max(deger) - min(deger)) / 2</code> - Ortalama fark</li>
                                <li><code>(ortalama(deger) * 1.18) - 5</code> - Formül uygulama</li>
                                <li><code>sqrt(topla((deger - ortalama(deger))^2) / sayi)</code> - Standart sapma</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Rule Modal -->
<div class="modal fade" id="testRuleModal" tabindex="-1" role="dialog" aria-labelledby="testRuleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testRuleModalLabel">Kural Test Et</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testRuleForm">
                    @csrf
                    <div class="form-group">
                        <label for="test_dataset_id">Veri Seti</label>
                        <select class="form-control" id="test_dataset_id" name="dataset_id" required>
                            <option value="">Seçiniz</option>
                            @foreach($datasets as $dataset)
                            <option value="{{ $dataset->id }}">{{ $dataset->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="test_rule">Kural İfadesi</label>
                        <textarea class="form-control" id="test_rule" name="rule" rows="3" 
                                  placeholder="ortalama(deger)" required></textarea>
                    </div>
                    <div class="form-group">
                        <div id="testResult" style="display: none;">
                            <hr>
                            <h5>Test Sonucu:</h5>
                            <div class="alert alert-success">
                                <strong id="resultValue">0</strong>
                                <span id="resultUnit"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="runTestBtn">
                    <i class="fas fa-play"></i> Testi Çalıştır
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Test Rule
        $('#runTestBtn').click(function() {
            const datasetId = $('#test_dataset_id').val();
            const rule = $('#test_rule').val();
            
            if (!datasetId || !rule) {
                alert('Lütfen tüm alanları doldurun!');
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
                        $('#resultValue').text(response.result !== null ? response.result.toFixed(4) : 'Hesaplanamadı');
                        
                        // Get unit from dataset
                        const datasets = @json($datasets->keyBy('id'));
                        if (datasets[datasetId]) {
                            $('#resultUnit').text(' ' + datasets[datasetId].unit);
                        }
                        
                        $('#testResult').show();
                    } else {
                        alert('Test başarısız: ' + (response.message || 'Bilinmeyen hata'));
                    }
                },
                error: function(xhr) {
                    alert('Sunucu hatası: ' + xhr.responseText);
                }
            });
        });
        
        // Auto-fill rule when dataset selected
        $('#test_dataset_id').change(function() {
            const datasetId = $(this).val();
            const datasets = @json($datasets->keyBy('id'));
            
            if (datasetId && datasets[datasetId] && datasets[datasetId].calculation_rule) {
                $('#test_rule').val(datasets[datasetId].calculation_rule);
            } else {
                $('#test_rule').val('');
            }
        });
    });
</script>
@endpush
