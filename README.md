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
git clone https://github.com/metatronslove/open-statistics-platform.git
cd open-statistics-platform
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

Proje ile ilgili soru soramazsın.

# Open Statistics Platform - InfinityFree Deployment Guide

## 📋 ÖN KOŞULLAR

1. **InfinityFree Hesabı** (ücretsiz)
2. **Domain/Subdomain** (örn: ose.infinityfreeapp.com)
3. **MySQL Database** (InfinityFree panelinden oluşturun)
4. **FileZilla veya benzeri FTP istemcisi**

## 🚀 DEPLOYMENT ADIMLARI

### 1. PROJE HAZIRLIĞI

```bash
# Yerelde projeyi hazırlayın
git clone [repo-url] ose-platform
cd ose-platform

# Gereksinimleri yükleyin (PHP 7.4)
composer install --no-dev --optimize-autoloader

# .env dosyasını InfinityFree için düzenleyin
cp .env.example .env
```

### 2. `.env` DOSYASI AYARLARI

```env
APP_NAME="Open Statistics Economy"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://yourdomain.epizy.com

# InfinityFree MySQL Ayarları
DB_CONNECTION=mysql
DB_HOST=sqlXXX.epizy.com
DB_PORT=3306
DB_DATABASE=epiz_XXX_XXX
DB_USERNAME=epiz_XXX
DB_PASSWORD=XXX

# Diğer ayarlar
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

### 3. FTP İLE YÜKLEME

1. **FileZilla'da bağlanın:**
   - Host: `ftpupload.net`
   - Username: `epiz_XXX`
   - Password: `XXX`
   - Port: `21`

2. **Dosyaları yükleyin:**
   ```
   /htdocs/ dizinine tüm dosyaları yükleyin
   AŞAĞIDAKİ DOSYALARI SİLİN:
   - .env.example (sadece .env kalacak)
   - composer.json (gerekli değil)
   - package.json (gerekli değil)
   - tests/ dizini (gerekli değil)
   ```

3. **Klasör izinlerini ayarlayın:**
   ```
   storage/ -> 755
   bootstrap/cache/ -> 755
   public/ -> 755
   ```

### 4. DATABASE KURULUMU

1. **InfinityFree panelinden:**
   - phpMyAdmin'e girin
   - epiz_XXX_XXX database'ini seçin

2. **SQL import edin:**
   ```sql
   -- Önce migrations'ı çalıştırın
   -- Sonra seed verilerini ekleyin
   
   -- Veya manuel olarak:
   CREATE TABLE users (...);
   CREATE TABLE datasets (...);
   -- vs.
   ```

### 5. ARTISAN KOMUTLARI (SSH üzerinden)

InfinityFree SSH erişimi için:

```bash
# SSH ile bağlanın
ssh epiz_XXX@yourdomain.epizy.com
# Password: XXX

# Proje dizinine gidin
cd htdocs

# Artisan komutlarını çalıştırın
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize:clear
```

### 6. CRON JOBS AYARLARI

InfinityFree panelinden Cron Jobs ekleyin:

```
# Her saat başı queue worker'ı çalıştır
0 * * * * curl http://yourdomain.epizy.com/queue/process

# Her gün gece yarısı backup al
0 0 * * * curl http://yourdomain.epizy.com/backup/create
```

### 7. PERFORMANS OPTİMİZASYONLARI

1. **`.htaccess` optimizasyonu:**
   ```apache
   # GZIP sıkıştırma
   <IfModule mod_deflate.c>
       AddOutputFilterByType DEFLATE text/html text/css application/javascript
   </IfModule>
   
   # Cache headers
   <IfModule mod_expires.c>
       ExpiresActive On
       ExpiresByType text/css "access plus 1 month"
       ExpiresByType application/javascript "access plus 1 month"
   </IfModule>
   ```

2. **Laravel optimizasyonları:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### 8. GÜVENLİK AYARLARI

1. **`.env` dosyasını koruyun:**
   ```apache
   # .htaccess ile
   <Files ".env">
       Order allow,deny
       Deny from all
   </Files>
   ```

2. **SQL injection koruması:**
   - Eloquent ORM kullanıldığı için otomatik

3. **XSS koruması:**
   - Blade templating otomatik escape yapar

### 9. YEDEKLEME STRATEJİSİ

1. **Database backup:**
   ```bash
   # Cron ile her gün
   mysqldump -u epiz_XXX -pXXX epiz_XXX_XXX > backup_$(date +%Y%m%d).sql
   ```

2. **Dosya backup:**
   ```bash
   # Tüm proje dosyaları
   tar -czf backup_$(date +%Y%m%d).tar.gz .
   ```

### 10. SIK KARŞILAŞILAN SORUNLAR

**Sorun 1:** "Permission denied" hatası
```bash
# Çözüm:
chmod 755 storage bootstrap/cache
```

**Sorun 2:** Database bağlantı hatası
```bash
# Çözüm:
# .env dosyasını kontrol edin
# InfinityFree panelinden database bilgilerini doğrulayın
```

**Sorun 3:** Sayfa yüklenmiyor
```bash
# Çözüm:
# public/index.php kontrol edin
# .htaccess dosyasını kontrol edin
```

## 📞 DESTEK

- **Proje Issues:** GitHub repository
- **InfinityFree Support:** https://infinityfree.net/support/
- **Laravel Documentation:** https://laravel.com/docs/8.x

## ✅ DEPLOYMENT CHECKLIST

- [ ] .env dosyası doğru ayarlandı
- [ ] Database bağlantısı test edildi
- [ ] Dosya izinleri ayarlandı
- [ ] Artisan komutları çalıştırıldı
- [ ] Ana sayfa yükleniyor
- [ ] Login/Register çalışıyor
- [ ] Database migrations başarılı
- [ ] Cron jobs ayarlandı
- [ ] Backup sistemi kuruldu
```

### **`storage/framework/` Dizin Yapısı**
```
storage/framework/
├── cache/
│   ├── data/
│   └── .gitignore
├── sessions/
│   └── .gitignore
├── views/
│   └── .gitignore
├── testing/
│   └── .gitignore
└── .gitignore
```

Her klasör için `.gitignore`:
```gitignore
*
!.gitignore
```

## ☕ Destek Olun / Support

Projemi beğendiyseniz, bana bir kahve ısmarlayarak destek olabilirsiniz!

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://buymeacoffee.com/metatronslove)

Teşekkürler! 🙏



