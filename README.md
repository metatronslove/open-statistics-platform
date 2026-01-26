# Open Statistics for Economy (OSE)

Açık İstatistik Platformu - Ekonomik veriler için şeffaf, çok kaynaklı veri toplama ve doğrulama platformu.

## 🌟 Özellikler

- **Çoklu Rol Sistemi**: Admin, İstatistikçi ve Veri Sağlayıcı rolleri
- **OAuth Girişi**: Google, GitHub ve Facebook ile giriş
- **Veri Doğrulama**: Otomatik outlier tespiti ve doğrulama
- **DSL Motoru**: Basit dil ile hesaplama kuralları tanımlama
- **Grafikler**: Chart.js ile veri görselleştirme
- **AdminLTE**: Modern ve responsive admin paneli

## 🚀 Kurulum

### Gereksinimler
- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js 16+

### 1. Projeyi Klonlayın
```bash
git clone https://github.com/yourusername/ose-platform.git
cd ose-platform
```

### 2. Bağımlılıkları Yükleyin
```bash
composer install
npm install
npm run build
```

### 3. Ortam Değişkenlerini Ayarlayın
```bash
cp .env.example .env
php artisan key:generate
```

`.env` dosyasını düzenleyin:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ose_database
DB_USERNAME=root
DB_PASSWORD=

# OAuth Ayarları
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_secret
GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback

FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

### 4. Veritabanını Kurun
```bash
php artisan migrate --seed
```

### 5. Storage Link Oluşturun
```bash
php artisan storage:link
```

### 6. Kuyruk İşlerini Başlatın (Opsiyonel)
```bash
php artisan queue:work
```

### 7. Uygulamayı Başlatın
```bash
php artisan serve
```

Tarayıcınızda http://localhost:8000 adresini açın.

## 👥 Varsayılan Kullanıcılar

Seeding sonrasında oluşturulan kullanıcılar:

| Email | Password | Role |
|-------|----------|------|
| admin@ose.com | password | Admin |
| statistician@ose.com | password | Statistician |
| provider@ose.com | password | Provider |

## 🏗️ Proje Yapısı

```
ose-project/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/         # Admin kontrollerleri
│   │   ├── Statistician/  # İstatistikçi kontrollerleri
│   │   ├── Provider/      # Veri sağlayıcı kontrollerleri
│   │   └── Auth/          # OAuth kontrolleri
│   ├── Models/           # Eloquent modeller
│   ├── Services/         # İş mantığı servisleri
│   ├── Jobs/             # Kuyruk işleri
│   └── Policies/         # Yetki kontrolleri
├── database/
│   ├── migrations/       # Veritabanı şeması
│   └── seeders/         # Test verileri
├── resources/
│   ├── views/           # Blade şablonları
│   │   ├── admin/       # Admin arayüzü
│   │   ├── statistician/# İstatistikçi arayüzü
│   │   └── provider/    # Veri sağlayıcı arayüzü
│   └── js/              # JavaScript dosyaları
└── routes/              # Route tanımları
```

## 🔧 Kullanım

### Admin
- Kullanıcı yönetimi
- Veri setleri görüntüleme
- Sistem istatistikleri

### İstatistikçi
- Veri seti oluşturma ve yönetme
- DSL ile hesaplama kuralları tanımlama
- Veri doğrulama süreçlerini izleme
- Grafiklerle veri analizi

### Veri Sağlayıcı
- Açık veri setlerine veri girme
- Kendi verilerini görüntüleme ve düzenleme
- Profil yönetimi

## 📊 DSL (Domain-Specific Language)

### Kullanılabilir Fonksiyonlar
- `ortalama(deger)` veya `mean(deger)`: Ortalama hesaplama
- `topla(deger)`: Toplam hesaplama
- `max(deger)`: Maksimum değer
- `min(deger)`: Minimum değer
- `sayi`: Veri noktası sayısı

### Örnek Kurallar
```dsl
ortalama(deger)
topla(deger) / sayi
(max(deger) - min(deger)) / 2
(ortalama(deger) * 1.18) - 5
```

## 🔒 Güvenlik

- CSRF koruması
- SQL Injection koruması (Eloquent ORM)
- XSS koruması (Blade templating)
- Role-based yetkilendirme
- Input validation

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'inize push edin (`git push origin feature/AmazingFeature`)
5. Pull Request açın

## 📞 İletişim

Proje ile ilgili sorularınız için: [email@example.com](mailto:email@example.com)
