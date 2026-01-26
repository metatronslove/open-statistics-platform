@extends('layouts.app')

@section('title', 'Kullanıcı Düzenle')
@section('page_title', 'Kullanıcı Düzenle: ' . $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Kullanıcılar</a></li>
    <li class="breadcrumb-item active">Düzenle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Kullanıcı Bilgilerini Düzenle</h3>
                </div>
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">İsim *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Yeni Şifre Tekrar</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>

                        <div class="form-group">
                            <label for="role">Rol *</label>
                            <select class="form-control @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Yönetici</option>
                                <option value="statistician" {{ old('role', $user->role) == 'statistician' ? 'selected' : '' }}>İstatistikçi</option>
                                <option value="provider" {{ old('role', $user->role) == 'provider' ? 'selected' : '' }}>Veri Sağlayıcı</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Veri Sağlayıcı için ek alanlar -->
                        <div id="providerFields" style="{{ $user->role == 'provider' ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="organization_name">Kurum/Kuruluş Adı</label>
                                <input type="text" class="form-control" 
                                       id="organization_name" name="organization_name" 
                                       value="{{ old('organization_name', $user->dataProvider->organization_name ?? '') }}">
                            </div>
                            
                            @if($user->dataProvider)
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="verify_provider" name="verify_provider" value="1"
                                           {{ $user->dataProvider->is_verified ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="verify_provider">
                                        Veri sağlayıcıyı doğrula
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Mevcut güven skoru: {{ $user->dataProvider->trust_score ?? 0 }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default">
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
        // Rol seçimine göre alanları göster/gizle
        $('#role').change(function() {
            if ($(this).val() === 'provider') {
                $('#providerFields').show();
                $('#organization_name').prop('required', true);
            } else {
                $('#providerFields').hide();
                $('#organization_name').prop('required', false);
            }
        });
    });
</script>
@endpush
