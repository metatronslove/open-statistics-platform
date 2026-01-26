@extends('layouts.app')

@section('title', 'Veri Seti Düzenle')
@section('page_title', 'Veri Seti Düzenle: ' . $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.datasets.show', $dataset) }}">{{ $dataset->name }}</a></li>
    <li class="breadcrumb-item active">Düzenle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Düzenle</h3>
                </div>
                <form action="{{ route('admin.datasets.update', $dataset) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Veri Seti Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $dataset->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $dataset->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unit">Birim *</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                   id="unit" name="unit" value="{{ old('unit', $dataset->unit) }}" required>
                            @error('unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="created_by">Oluşturan *</label>
                            <select class="form-control @error('created_by') is-invalid @enderror" 
                                    id="created_by" name="created_by" required>
                                @foreach($users as $user)
                                    @if(in_array($user->role, ['admin', 'statistician']))
                                        <option value="{{ $user->id }}" 
                                                {{ old('created_by', $dataset->created_by) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('created_by')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="calculation_rule">Hesaplama Kuralı (DSL)</label>
                            <textarea class="form-control @error('calculation_rule') is-invalid @enderror" 
                                      id="calculation_rule" name="calculation_rule" rows="4">{{ old('calculation_rule', $dataset->calculation_rule) }}</textarea>
                            @error('calculation_rule')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Kullanılabilir fonksiyonlar: ortalama(), topla(), max(), min(), sayi, stddev()
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="is_public" name="is_public" value="1" 
                                       {{ old('is_public', $dataset->is_public) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_public">
                                    Veri seti herkese açık olsun
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.datasets.show', $dataset) }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
