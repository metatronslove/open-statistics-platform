<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Şifre Onayı - Open Statistics Economy</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="{{ route('home') }}">
            <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="OSE Logo" style="height: 50px;">
            <br>
            <b>Open Statistics</b> Economy
        </a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <div class="text-center mb-4">
                <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                <h4>Şifre Onayı Gerekli</h4>
            </div>

            <p class="text-center">
                Bu işlemi gerçekleştirebilmek için lütfen şifrenizi tekrar girin.
                Bu, hesabınızın güvenliğini sağlamak içindir.
            </p>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                
                <div class="input-group mb-3">
                    <input type="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Şifre" 
                           required 
                           autofocus>
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

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-check mr-2"></i> Şifreyi Onayla
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="row">
                <div class="col-12">
                    <p class="mb-0 text-center">
                        <a href="{{ route('home') }}">Ana sayfaya dön</a>
                    </p>
                </div>
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
