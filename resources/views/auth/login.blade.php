@extends('layouts.app')

@section('title', 'Giriş Yap')
@section('page_title', 'Giriş Yap')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Giriş Yap') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Adresi') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Şifre') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Beni Hatırla') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Giriş Yap') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Şifrenizi mi unuttunuz?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <!-- OAuth Login Buttons -->
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <p class="mb-3">Veya sosyal medya hesaplarınızla giriş yapın:</p>
                            
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('auth.google') }}" class="btn btn-danger">
                                    <i class="fab fa-google"></i> Google ile Giriş
                                </a>
                                <a href="{{ route('auth.github') }}" class="btn btn-dark">
                                    <i class="fab fa-github"></i> GitHub ile Giriş
                                </a>
                                <a href="{{ route('auth.facebook') }}" class="btn btn-primary">
                                    <i class="fab fa-facebook"></i> Facebook ile Giriş
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <p>Hesabınız yok mu? 
                                <a href="{{ route('register') }}">Kayıt Olun</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-danger, .btn-dark, .btn-primary {
        width: 150px;
    }
</style>
@endpush
