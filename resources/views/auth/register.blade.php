<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayıt Ol - Open Statistics Economy</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <div class="register-logo">
        <a href="{{ route('home') }}">
            <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="OSE Logo" style="height: 50px;">
            <br>
            <b>Open Statistics</b> Economy
        </a>
    </div>

    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Yeni hesap oluşturun</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="input-group mb-3">
                    <input type="text" 
                           name="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="İsim Soyisim" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Email" 
                           value="{{ old('email') }}" 
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Şifre" 
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="password" 
                           name="password_confirmation" 
                           class="form-control" 
                           placeholder="Şifre Tekrar" 
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
                            <label for="agreeTerms">
                                <a href="#" data-toggle="modal" data-target="#termsModal">Kullanım şartlarını</a> kabul ediyorum
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            Kayıt Ol
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="social-auth-links text-center">
                <p>- VEYA -</p>
                <div class="row">
                    <div class="col-12 mb-2">
                        <a href="{{ route('auth.google') }}" class="btn btn-block btn-danger">
                            <i class="fab fa-google mr-2"></i> Google ile Kayıt Ol
                        </a>
                    </div>
                    <div class="col-12 mb-2">
                        <a href="{{ route('auth.github') }}" class="btn btn-block btn-dark">
                            <i class="fab fa-github mr-2"></i> GitHub ile Kayıt Ol
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('auth.facebook') }}" class="btn btn-block btn-primary">
                            <i class="fab fa-facebook mr-2"></i> Facebook ile Kayıt Ol
                        </a>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-12">
                    <p class="mb-0 text-center">
                        Zaten hesabınız var mı? 
                        <a href="{{ route('login') }}" class="text-center">Giriş yapın</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Kullanım Şartları</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Open Statistics Economy Platformu Kullanım Şartları</h5>
                <p>1. Bu platform şeffaf ve açık istatistik verileri için oluşturulmuştur.</p>
                <p>2. Kullanıcılar doğru ve güvenilir veriler girmekle yükümlüdür.</p>
                <p>3. Platformda paylaşılan veriler herkes tarafından görüntülenebilir.</p>
                <p>4. Sistem otomatik doğrulama mekanizmaları kullanır.</p>
                <p>5. Yanlış veya manipülatif veri giren kullanıcıların hesapları askıya alınabilir.</p>
                <p>6. Tüm veriler açık kaynak lisansı ile paylaşılır.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
