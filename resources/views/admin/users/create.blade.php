@extends('layouts.app')

@section('title', 'Yeni Kullanıcı')
@section('page_title', 'Yeni Kullanıcı Oluştur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Kullanıcılar</a></li>
    <li class="breadcrumb-item active">Yeni Oluştur</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Yeni Kullanıcı Bilgileri</h3>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">İsim *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Şifre *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Şifre Tekrar *</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Rol *</label>
                            <select class="form-control @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="">Seçiniz</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Yönetici</option>
                                <option value="statistician" {{ old('role') == 'statistician' ? 'selected' : '' }}>İstatistikçi</option>
                                <option value="provider" {{ old('role') == 'provider' ? 'selected' : '' }}>Veri Sağlayıcı</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Veri Sağlayıcı için ek alanlar -->
                        <div id="providerFields" style="display: none;">
                            <div class="form-group">
                                <label for="organization_name">Kurum/Kuruluş Adı</label>
                                <input type="text" class="form-control" 
                                       id="organization_name" name="organization_name" 
                                       value="{{ old('organization_name') }}">
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="verify_provider" name="verify_provider" value="1">
                                    <label class="custom-control-label" for="verify_provider">
                                        Veri sağlayıcıyı hemen doğrula
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
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
        
        // Sayfa yüklendiğinde kontrol et
        $('#role').trigger('change');
    });
</script>
@endpush
