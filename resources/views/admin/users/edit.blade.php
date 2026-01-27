@extends('layouts.app')

@section('title', 'Kullanıcı Düzenle')
@section('page_title', 'Kullanıcı Düzenle: ' . $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Kullanıcılar</a></li>
    <li class="breadcrumb-item active">Düzenle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Kullanıcı Bilgileri</h3>
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
                            <label for="email">Email Adresi *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password">
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation">Şifre Tekrar</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Rol *</label>
                            <select class="form-control @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="">Seçiniz</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="statistician" {{ old('role', $user->role) == 'statistician' ? 'selected' : '' }}>İstatistikçi</option>
                                <option value="provider" {{ old('role', $user->role) == 'provider' ? 'selected' : '' }}>Veri Sağlayıcı</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div id="providerFields" style="display: {{ $user->role === 'provider' || old('role') === 'provider' ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label for="organization_name">Kurum Adı *</label>
                                <input type="text" class="form-control @error('organization_name') is-invalid @enderror" 
                                       id="organization_name" name="organization_name" 
                                       value="{{ old('organization_name', $user->dataProvider->organization_name ?? '') }}"
                                       {{ $user->role === 'provider' || old('role') === 'provider' ? 'required' : '' }}>
                                @error('organization_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                       id="verify_provider" name="verify_provider" value="1" 
                                       {{ old('verify_provider', $user->dataProvider->is_verified ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="verify_provider">Veri sağlayıcıyı doğrula</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default">İptal</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Kullanıcı Bilgileri</h3>
                </div>
                <div class="card-body">
                    <p><strong>Kayıt Tarihi:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                    <p><strong>Son Güncelleme:</strong> {{ $user->updated_at->format('d.m.Y H:i') }}</p>
                    
                    @if($user->email_verified_at)
                        <p><strong>Email Doğrulama:</strong> 
                            <span class="badge bg-success">Doğrulanmış</span>
                        </p>
                    @else
                        <p><strong>Email Doğrulama:</strong> 
                            <span class="badge bg-warning">Bekliyor</span>
                        </p>
                    @endif
                    
                    @if($user->dataProvider)
                        <hr>
                        <h5>Veri Sağlayıcı Bilgileri</h5>
                        <p><strong>Kurum:</strong> {{ $user->dataProvider->organization_name }}</p>
                        @if($user->dataProvider->website)
                            <p><strong>Website:</strong> {{ $user->dataProvider->website }}</p>
                        @endif
                        <p><strong>Doğrulama:</strong> 
                            @if($user->dataProvider->is_verified)
                                <span class="badge bg-success">Doğrulanmış</span>
                            @else
                                <span class="badge bg-warning">Bekliyor</span>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
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
