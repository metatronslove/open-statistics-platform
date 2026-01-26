<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Open Statistics for Economy</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="OSE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-bold">Open Statistics Economy</span>
            </a>
            
            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="#about" class="nav-link">Hakkında</a>
                    </li>
                    <li class="nav-item">
                        <a href="#features" class="nav-link">Özellikler</a>
                    </li>
                    <li class="nav-item">
                        <a href="#how-it-works" class="nav-link">Nasıl Çalışır?</a>
                    </li>
                    <li class="nav-item">
                        <a href="#contact" class="nav-link">İletişim</a>
                    </li>
                    @guest
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link btn btn-primary ml-2 text-white">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="nav-link btn btn-success ml-2 text-white">Kayıt Ol</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link btn btn-primary ml-2 text-white">Panele Git</a>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
        <!-- Hero Section -->
        <section class="hero bg-primary text-white py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 font-weight-bold mb-4">Açık İstatistik Platformu</h1>
                        <p class="lead mb-4">
                            Ekonomik veriler için şeffaf, çok kaynaklı veri toplama ve doğrulama platformu.
                            "Her vatandaş kendi istatistik kurumunu kurabilir."
                        </p>
                        <div class="mt-4">
                            @guest
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg mr-3">
                                <i class="fas fa-rocket mr-2"></i> Hemen Başla
                            </a>
                            <a href="#how-it-works" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-play-circle mr-2"></i> Nasıl Çalışır?
                            </a>
                            @else
                            <a href="{{ route('home') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard'a Git
                            </a>
                            @endguest
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="https://cdn.pixabay.com/photo/2018/05/08/08/44/artificial-intelligence-3382507_1280.jpg" 
                             alt="Statistics Visualization" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center mb-5">
                        <h2 class="section-title">Open Statistics Economy Nedir?</h2>
                        <p class="lead text-muted">
                            TÜİK'in 2016 öncesi metodolojisiyle, şeffaf, çoklu kaynaktan veri toplayan, 
                            doğrulayan ve alternatif ekonomik istatistikler üreten açık kaynak bir portal.
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                                <h4 class="card-title">Şeffaflık</h4>
                                <p class="card-text">
                                    Tüm veriler ve hesaplama metodları herkesin erişimine açıktır.
                                    Manipülasyona kapalı, doğrulanabilir bir sistem.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-success mb-3"></i>
                                <h4 class="card-title">Topluluk</h4>
                                <p class="card-text">
                                    Binlerce veri sağlayıcı, istatistikçi ve vatandaşın 
                                    katkılarıyla oluşan kolektif bir bilgi havuzu.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-cogs fa-3x text-warning mb-3"></i>
                                <h4 class="card-title">Teknoloji</h4>
                                <p class="card-text">
                                    Gelişmiş doğrulama algoritmaları, DSL hesaplama motoru 
                                    ve gerçek zamanlı veri işleme teknolojileri.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center mb-5">
                        <h2 class="section-title">Platform Özellikleri</h2>
                        <p class="lead text-muted">
                            Modern teknolojilerle donatılmış, kapsamlı istatistik platformu
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-user-shield text-primary mr-2"></i>
                                    Çoklu Rol Sistemi
                                </h4>
                                <p class="card-text">
                                    <strong>Admin:</strong> Sistem yönetimi ve denetim<br>
                                    <strong>İstatistikçi:</strong> Veri seti ve hesaplama kuralı yönetimi<br>
                                    <strong>Veri Sağlayıcı:</strong> Veri girişi ve doğrulama
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-robot text-success mr-2"></i>
                                    Otomatik Doğrulama
                                </h4>
                                <p class="card-text">
                                    3 sigma kuralı ile otomatik outlier tespiti<br>
                                    Çoklu kaynak doğrulama sistemi<br>
                                    Gerçek zamanlı veri kalite kontrolü
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-code text-warning mr-2"></i>
                                    DSL Hesaplama Motoru
                                </h4>
                                <p class="card-text">
                                    Basit dil ile kompleks hesaplamalar<br>
                                    ortalama(), topla(), max(), min(), stddev() fonksiyonları<br>
                                    Matematiksel operatör destekli
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-chart-line text-danger mr-2"></i>
                                    Gelişmiş Görselleştirme
                                </h4>
                                <p class="card-text">
                                    Chart.js ile interaktif grafikler<br>
                                    Zaman serisi analizi<br>
                                    Dashboard widget sistemli
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center mb-5">
                        <h2 class="section-title">Nasıl Çalışır?</h2>
                        <p class="lead text-muted">
                            4 basit adımda alternatif istatistikler üretin
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="step-card text-center">
                            <div class="step-number">1</div>
                            <h4>Kayıt Ol</h4>
                            <p>Google, GitHub veya email ile ücretsiz kayıt olun</p>
                            <i class="fas fa-user-plus fa-2x text-primary"></i>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="step-card text-center">
                            <div class="step-number">2</div>
                            <h4>Veri Gir</h4>
                            <p>Market fiyatları, enflasyon verileri gibi ekonomik verileri girin</p>
                            <i class="fas fa-edit fa-2x text-success"></i>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="step-card text-center">
                            <div class="step-number">3</div>
                            <h4>Doğrula</h4>
                            <p>Sistem otomatik olarak verileri doğrular ve aykırıları işaretler</p>
                            <i class="fas fa-check-circle fa-2x text-warning"></i>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="step-card text-center">
                            <div class="step-number">4</div>
                            <h4>Hesapla</h4>
                            <p>DSL ile özel hesaplamalar yapın ve sonuçları görselleştirin</p>
                            <i class="fas fa-calculator fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-5 bg-dark text-white">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="stat-item">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <h3 class="counter" data-target="1000">0</h3>
                            <p>Aktif Kullanıcı</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="stat-item">
                            <i class="fas fa-database fa-3x mb-3"></i>
                            <h3 class="counter" data-target="5000">0</h3>
                            <p>Veri Seti</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="stat-item">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <h3 class="counter" data-target="250000">0</h3>
                            <p>Veri Noktası</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="stat-item">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h3 class="counter" data-target="98">0</h3>
                            <p>Doğrulama Oranı (%)</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-5 bg-primary text-white">
            <div class="container text-center">
                <h2 class="mb-4">Hemen Katılın ve Alternatif İstatistikler Üretmeye Başlayın!</h2>
                <p class="lead mb-4">
                    Açık, şeffaf ve doğrulanabilir ekonomik veriler için topluluğumuza katılın.
                </p>
                @guest
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-rocket mr-2"></i> Ücretsiz Kayıt Ol
                </a>
                @else
                <a href="{{ route('home') }}" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard'a Git
                </a>
                @endguest
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Open Statistics Economy</h5>
                    <p>
                        Açık kaynak istatistik platformu.<br>
                        Her vatandaş kendi istatistik kurumunu kurabilir.
                    </p>
                </div>
                
                <div class="col-md-4">
                    <h5>Hızlı Bağlantılar</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('login') }}" class="text-white">Giriş Yap</a></li>
                        <li><a href="{{ route('register') }}" class="text-white">Kayıt Ol</a></li>
                        <li><a href="#about" class="text-white">Hakkında</a></li>
                        <li><a href="#features" class="text-white">Özellikler</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5>İletişim</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope mr-2"></i> info@opensatistics.org</li>
                        <li><i class="fas fa-globe mr-2"></i> www.opensatistics.org</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> İstanbul, Türkiye</li>
                    </ul>
                </div>
            </div>
            
            <hr class="bg-white">
            
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0">
                        &copy; {{ date('Y') }} Open Statistics Economy. Tüm hakları saklıdır.
                        <br>
                        <small>Açık kaynak - MIT Lisansı</small>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
    // Simple counter animation
    $(document).ready(function() {
        $('.counter').each(function() {
            var $this = $(this);
            var target = $this.data('target');
            var current = 0;
            var increment = target / 100;
            
            var timer = setInterval(function() {
                if (current < target) {
                    current += increment;
                    $this.text(Math.floor(current));
                } else {
                    $this.text(target);
                    clearInterval(timer);
                }
            }, 20);
        });
        
        // Smooth scroll
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top
                }, 1000);
            }
        });
    });
</script>

<style>
    .hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        width: 50%;
        height: 4px;
        background: #667eea;
        bottom: -10px;
        left: 25%;
        border-radius: 2px;
    }
    
    .step-card {
        padding: 2rem;
        border-radius: 10px;
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .step-card:hover {
        transform: translateY(-10px);
    }
    
    .step-number {
        width: 60px;
        height: 60px;
        background: #667eea;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0 auto 1rem;
    }
    
    .stat-item i {
        color: #667eea;
    }
    
    .stat-item h3 {
        font-size: 2.5rem;
        font-weight: bold;
    }
</style>
</body>
</html>
