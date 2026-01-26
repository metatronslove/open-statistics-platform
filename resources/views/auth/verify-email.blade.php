<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Doğrulama - Open Statistics Economy</title>
    
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
                <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                <h4>Email Doğrulama Gerekli</h4>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success" role="alert">
                    Yeni bir doğrulama linki email adresinize gönderildi.
                </div>
            @endif

            <p class="text-center">
                Devam etmeden önce lütfen email adresinize gönderdiğimiz doğrulama linkine tıklayın.
                Eğer email almadıysanız, aşağıdaki butona tıklayarak yenisini talep edebilirsiniz.
            </p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane mr-2"></i> Doğrulama Email'i Tekrar Gönder
                        </button>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-default btn-block">
                            <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
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
