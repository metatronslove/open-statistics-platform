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
