# Web Sitesi Dizin Dokümantasyonu

**Oluşturulma Tarihi:** 2026-01-28 01:34:29  
**Kök Dizin:** `D:\open-statistics-platform`  
**Toplam Dosya Sayısı:** 0

---

## Dizin Yapısı ve Dosya İçerikleri

📄 **ai-made-this.md**
```markdown
Aşağıdaki prompt ile başlayarak amaca yönelik yukarıdaki dosyada bulunan kodları içeren dosyaları ürettik. Mevcut dosyaların yeterli ve sitenin çalışabilmesi için her hangi bir eksik dosya olup olmadığını veya mevcut dosyalarda tamamlanmamış ve not olarak daha sonra tamamlanması planlanmış bir eksik var mı bunu da bilmiyorum. Amacım bu open statistics platform'u infinityfree üzerinde çalışır hale getirmek, gereken bileşenleri yerelde composer ve node.js kurmadan önce yapmamız gerekenin, eksik hiç bir dosya ve bölüm kalmadığından emin olmamız gerektiğini düşünüyorum, infinityfree için php sürümünü 7.4'e düşürmek gerekiyorsa bile hazır sistemdeki özellikler kaybedilmemelidir. Ya da diğer downgradeler:

***
# **Open Statistics Platform (OSE) - Complete Development Prompt**

**Göreviniz:** Aşağıdaki tüm gereksinimleri, mimariyi ve detayları kullanarak, "Open Statistics for Economy" (Açık İstatistik Platformu) adlı tam teşekküllü bir web uygulamasının tüm kaynak kod dosyalarını üretin. Bu prompt, eksiksiz bir proje spesifikasyonudur.

### **Ana Prompt: "Open Statistics for Economy - Tam Proje Üretimi"**

**Görev:** Aşağıda teknik gereksinimleri tam olarak tanımlanmış, Laravel tabanlı bir web uygulamasının TÜM dosyalarını (klasör yapısıyla birlikte) oluşturacaksın. Proje, InfinityFree (PHP 7.4+, MySQL 5.6+) hosting ortamında çalışacak şekilde optimize edilmelidir.

**Proje Adı:** Open Statistics for Economy (OSE)

**Temel Teknoloji:** Laravel 8.x (istikrarlı sürüm), MySQL, Bootstrap 5 (AdminLTE tarzı), Chart.js, Laravel Socialite.

**Amaç:** Kullanıcıların alternatif istatistiksel veriler girip doğrulayabileceği, özel hesaplama kuralları tanımlayabileceği ve enflasyon gibi gerçek zamanlı göstergeler üretebileceği şeffaf bir portal.

**DETAYLI SİSTEM GEREKSİNİMLERİ:**

**1. VERİTABANI YAPISI (Migrasyonlar Oluştur):**
- `users`: id, name, email, password, role (enum: 'admin', 'statistician', 'provider'), avatar, provider_id (OAuth için), email_verified_at, remember_token, timestamps.
- `data_providers`: id, user_id (foreign), organization_name, description, website, is_verified, timestamps.
- `datasets`: id, name (örn: "Sigara Fiyatları"), slug, description, calculation_rule (text, DSL için), is_public, created_by, timestamps.
- `data_points`: id, dataset_id (foreign), data_provider_id (foreign), date, value (decimal), source_url (nullable), is_verified (boolean), verified_value (decimal, nullable), notes (nullable), timestamps.
- `validation_logs`: id, dataset_id, date, calculated_average, standard_deviation, status, timestamps.
- `password_resets`, `migrations`, `failed_jobs`, `personal_access_tokens` (standart Laravel tabloları).

**2. KİMLİK DOĞRULAMA & YETKİLENDİRME:**
- Laravel'in built-in `auth` scaffold'u.
- **Laravel Socialite** ile Google ve GitHub girişi (Facebook opsiyonel). Ayarlar `.env` dosyasından yapılacak.
- **Roller ve Yetkiler (Policies Kullan):**
    - `admin`: Her şeyi yönetir (Kullanıcı, Veri Seti, tüm veriler).
    - `statistician`: Veri seti oluşturur/duzenler, hesaplama kuralı yazar, tüm verileri görür, doğrulama raporlarını görür.
    - `provider`: Sadece kendi atanmış veri setlerine `data_points` girer/günceller. Başka veriyi görmez/düzenlemez.

**3. DASHBOARD ARAYÜZÜ (AdminLTE Tarzı):**
- `resources/views/layouts/app.blade.php`: Ana şablon, Bootstrap 5, sidebar (rollere göre menü dinamik).
- Dashboard (`/home`): Her rol için farklı widget'lar.
    - *Admin:* Sistem istatistikleri (kullanıcı sayısı, veri seti, vs.).
    - *Statistician:* Sorumlu olduğu veri setleri, doğrulama bekleyen veriler, hesaplama sonuçları.
    - *Provider:* Veri girişi için hızlı bağlantılar, son ekledikleri.
- **Dinamik Bileşen Sistemi:** Kullanıcı dashboard'ına "Hesaplanacak Değerler" veya "Veri Yığınları" gibi bileşenler ekleyip çıkarabilir (drag-drop değil, basit bir AÇIK/KAPALI arayüzü).

**4. VERİ YÖNETİMİ MODÜLLERİ:**
- **Veri Seti CRUD:** Sadece `statistician` ve `admin` oluşturabilir. Oluştururken `calculation_rule` (DSL) metin alanı.
- **Veri Noktası Girişi:** `provider` rolü, kendine atanmış veri setleri için `data_points` girer. Tarih elle seçilebilir (geçmiş tarih serbest).
- **DOĞRULAMA ALGORİTMASI (CRON Jobsız):** Bir veri seti için aynı `date` de 2+ `data_points` varsa, otomatik olarak bir `ValidationJob` tetiklenir (Laravel Queue kullan). Bu job:
    1. İlgili tarihteki tüm verileri getirir.
    2. Ortalama, standart sapma hesaplar.
    3. Her bir veri noktasını, ortalama ± 2*standart sapma aralığında mı diye kontrol eder. Aykırı olanları `is_verified=false` yapar.
    4. Ortalamayı `verified_value` olarak kaydeder ve `validation_logs` tablosuna bir kayıt atar.
- **Zaman Serisi Grafiği:** Her veri seti detay sayfasında, `Chart.js` ile `verified_value`'ları gösteren bir çizgi grafiği.

**5. HESAPLAMA MOTORU (DSL - Basit Sözdizimi):**
- `calculation_rule` örnekleri: `ortalama(deger)`, `topla(deger) / sayi`, `(max(deger) - min(deger)) / 2`.
- `App\Services\CalculationEngine` adında bir sınıf oluştur. `calculate($dataset_id)` metodu:
    1. `datasets` tablosundan `calculation_rule` metnini alır.
    2. Basit bir regex/parser ile `ortalama`, `topla`, `min`, `max`, `sayi` gibi anahtar kelimeleri ve matematiksel işlemleri yorumlar.
    3. `data_points` tablosundan `verified_value`'ları çekerek hesaplamayı yapar ve sonucu döndürür.
    4. Bu sonuç, dashboard'da veya veri seti sayfasında gösterilir.

**6. INFINITYFREE UYUMU & DEPLOYMENT AYARLARI:**
- `.env.example` dosyasında InfinityFree için örnek ayarlar:
    ```
    APP_URL=http://yourdomain.epizy.com
    DB_HOST=sqlXXX.epizy.com
    DB_DATABASE=epiz_XXX_XXX
    DB_USERNAME=epiz_XXX
    DB_PASSWORD=XXX
    ```
- `config/filesystems.php`'de disk olarak `'public'` kullan, `'default' => env('FILESYSTEM_DISK', 'public'),`
- `config/session.php`'de `'driver' => 'file',` (InfinityFree'de redis/database zor).
- `storage/` ve `bootstrap/cache/` klasörlerinin yazılabilir olduğundan emin ol.

**7. DOSYA YAPISI (OLUŞTURULACAK ÖNEMLİ DOSYALAR):**
- Tüm standart Laravel 8 yapısı (`app/Http/Controllers`, `app/Models`, `resources/views`, `routes/`, `database/migrations`, `database/seeders`).
- **Özel Dosyalar:**
    - `app/Http/Controllers/Admin/`: DatasetController, DataPointController, UserController, ValidationController.
    - `app/Models/`: User, DataProvider, Dataset, DataPoint, ValidationLog.
    - `app/Policies/`: DatasetPolicy, DataPointPolicy, UserPolicy.
    - `app/Services/CalculationEngine.php`.
    - `app/Jobs/ProcessValidationJob.php`.
    - `resources/views/dashboard/`: admin.blade.php, statistician.blade.php, provider.blade.php.
    - `resources/views/datasets/`: index.blade.php, show.blade.php (grafik burada), create.blade.php, edit.blade.php.
    - `resources/views/data-points/`: create.blade.php, my_data.blade.php.
    - `routes/web.php` (route tanımları dahilinde).

**İSTEK:** Yukarıdaki tüm spesifikasyonlara uygun, çalışır durumda bir Laravel projesinin TÜM dosya içeriklerini, doğru klasör yapısıyla birlikte oluştur. Her bir dosyanın içeriği, Laravel best practices'e uygun, eksiksiz (<?php ... ?> tag'leri dahil) ve birbiriyle entegre olacak şekilde yazılmalıdır. İlk önce `composer.json`, `.env.example`, ana migration dosyaları ve `routes/web.php` gibi kritik yapılandırma dosyalarını oluştur. Ardından Controller, Model, View ve Service dosyalarını sırayla oluştur.

**ÖNEMLİ NOT:** Bu prompt, bir sohbetin ilk mesajıdır. Eğer tek bir yanıtta tüm dosyalar için yeterli çıktı kapasitesine (token limiti) ulaşılamazsa, senin yanıtın şu şekilde bitecektir: "[DOSYA OLUŞTURMA DEVAM EDİYOR: Bir sonraki mesajda `app/Http/Controllers/Admin/DatasetController.php` ve `resources/views/datasets/index.blade.php` dosyalarının içeriğini oluşturacağım. Lütfen 'Devam et' de.]" Bu sayede ben "Devam et" veya "Eksik kalan dosyaları tamamla lütfen" yazarak sohbeti sürdürebilir ve tüm dosyalara kademeli olarak ulaşabilirim. Projenin tamamlanabilmesi için bu sohbet zincirinin devam etmesi kritiktir.

## **1. PROJE GENEL BAKIŞ**
**Proje Adı:** Open Statistics for Economy (OSE) / Açık İstatistik Platformu
**Amaç:** TÜİK'in 2016 öncesi metodolojisiyle, şeffaf, çoklu kaynaktan veri toplayan, doğrulayan ve vatandaşların alternatif ekonomik istatistikler (enflasyon, maaş zammı vb.) üretebildiği açık kaynak bir portal.
**Çekirdek Felsefe:** "Her vatandaş kendi istatistik kurumunu kurabilir."

## **2. TEKNOLOJİ STACK'I (Kesin)**
*   **Backend Framework:** PHP 8.2+ üzerine Laravel 11
*   **Veritabanı:** MySQL 8.0 (Ana veritabanı)
*   **Frontend Tema/Dashboard:** AdminLTE 4 (Bootstrap 5 tabanlı) - Responsive ve minimalist.
*   **Kimlik Doğrulama (Authentication):** Laravel Breeze veya Jetstream starter kit'i kullanılacak, ancak **Google, Facebook ve GitHub OAuth** entegrasyonu `Laravel Socialite` paketiyle mutlaka eklenecek.
*   **Grafikler:** Chart.js veya Laravel için uyumlu bir wrapper.
*   **Kural Motoru (Rule Engine):** `php-ruler/ruler` veya benzeri bir DSL kütüphanesi entegre edilecek.

## **3. DETAYLI VERİTABANI ŞEMASI (MySQL)**
Aşağıdaki tabloları ve ilişkileri içeren bir migration dosyaları seti oluşturun. Her model için Eloquent Model sınıfları yazın.

**A. Çekirdek Tablolar:**
1.  **`users`** (Laravel standart + ek alanlar):
    *   `id`, `name`, `email`, `email_verified_at`, `password`, `role` (`admin`, `statistician`, `data_provider`), `avatar` (OAuth'tan gelebilir), `provider_id` (OAuth), `provider_name`, `remember_token`, `timestamps`.
2.  **`data_providers`** (Veri Sağlayıcı Kuruluş/Kaynak):
    *   `id`, `user_id` (ilişki), `organization_name`, `website`, `description`, `trust_score` (sistem tarafından hesaplanan, 0-100), `is_approved` (bool), `timestamps`.
3.  **`datasets`** (Veri Setleri / "Sigara Fiyatları", "TÜFE Sepeti"):
    *   `id`, `name`, `slug`, `description`, `unit` ("TL", "USD", "Adet"), `created_by` (user_id), `is_public` (bool), `timestamps`.
4.  **`data_points`** (Ham Veri Noktaları):
    *   `id`, `dataset_id`, `data_provider_id` (user_id), `date` (tarih), `value` (decimal), `source_url` (doğrulama linki), `is_verified` (bool, default false), `verified_at`, `timestamps`. -> **Birleşik Unique Key:** (`dataset_id`, `data_provider_id`, `date`).
5.  **`verified_data`** (Doğrulanmış Veriler):
    *   `id`, `dataset_id`, `date`, `verified_value` (ortalama/medyan), `calculation_method` ("mean", "median"), `providers_count`, `standard_deviation`, `timestamps`. -> **Birleşik Unique Key:** (`dataset_id`, `date`).
6.  **`calculation_rules`** (Hesaplama Kuralları - DSL):
    *   `id`, `name` ("Sigara Tek Dal Maliyeti"), `slug`, `description`, `rule_expression` (DSL metni, örn: `avg(dataset_1) / 20`), `output_dataset_id` (oluşturduğu sanal veri seti), `created_by`, `timestamps`.
7.  **`rule_evaluation_logs`** (Kural Çalıştırma Geçmişi):
    *   `id`, `calculation_rule_id`, `executed_at`, `input_parameters` (json), `result` (json), `timestamps`.

**B. İlişkiler (Eloquent Relationships):**
*   `User` *hasOne* `DataProvider` (isteğe bağlı)
*   `User` *hasMany* `Dataset` (yaratılanlar)
*   `DataProvider` *hasMany* `DataPoint`
*   `Dataset` *hasMany* `DataPoint`
*   `Dataset` *hasMany* `VerifiedData`
*   `CalculationRule` *belongsTo* `Dataset` (output için)

## **4. KAPSAMLI İŞ MANTIĞI (Business Logic) VE KONTROLLER**
Her biri için tam controller, request validation ve service class'ları yazın.

**A. Kullanıcı Yetkilendirme ve Dashboard Yönlendirme:**
*   OAuth (Socialite) ile giriş. `role` değerine göre login sonrası yönlendirme:
    *   `admin`: `/admin/dashboard` (tüm sistem ayarları, kullanıcı onayı)
    *   `statistician`: `/statistician/dashboard` (veri seti ve kural yönetimi)
    *   `data_provider`: `/provider/dashboard` (sadece veri girişi)
*   Middleware: `auth`, `role:admin`, `role:statistician`, `role:data_provider`.

**B. Veri Sağlama ve Otomatik Doğrulama Süreci:**
1.  Bir `data_provider`, bir `dataset` için (`dataset_id`, `date`, `value`) girince `DataPointController@store` tetiklenir.
2.  Aynı `date` ve `dataset_id` için MEVCUT diğer `DataPoint` kayıtları kontrol edilir.
3.  Eğer 2 veya daha fazla farklı sağlayıcıdan veri varsa, bir **doğrulama job'ı** (`VerifyDataJob`) kuyruğa eklenir.
4.  Bu job şunları yapar:
    *   İlgili tarihteki tüm değerleri getirir.
    *   Ortalama, standart sapma hesaplar.
    *   **3 sigma kuralına göre aykırı değerleri (outlier) işaretler.**
    *   Ayıkırı olmayan değerlerin ortalamasını/medyanını alır.
    *   `verified_data` tablosuna bu doğrulanmış değeri yazar (varsa günceller).
    *   İlgili `data_point` kayıtlarının `is_verified` alanını günceller.

**C. İstatistikçi DSL (Domain-Specific Language) Arayüzü:**
*   `/statistician/rules/create` sayfasında bir form olacak.
*   Form alanları: `Name`, `Description`, `Datasets` (dropdown'dan seçilebilir), `Rule Expression`.
*   **DSL Sözdizimi Örneği ve Kullanılabilir Fonksiyonlar:**
    *   `avg(dataset_slug)` veya `mean(dataset_slug)`: Bir veri setinin doğrulanmış değerlerinin ortalaması.
    *   `median(dataset_slug)`: Medyan.
    *   `sum(dataset_slug)`: Toplam.
    *   `last(dataset_slug, n)`: Son N kayıt.
    *   `diff(dataset_slug)`: Bir önceki dönemle fark.
    *   `rate(dataset_slug)`: Bir önceki döneme göre değişim oranı.
    *   Operatörler: `+`, `-`, `*`, `/`, `()`.
    *   **Örnek Kural İfadeleri:**
        *   `avg(sigara_fiyatlari) / 20`
        *   `(last(enflasyon_sepeti, 1) / last(enflasyon_sepeti, 13) - 1) * 100`
*   Arka planda bu ifade parser'dan geçirilip `php-ruler` veya özel bir `RuleEvaluator` service'i ile yorumlanacak ve sonuç, `output_dataset_id` ile ilişkilendirilmiş sanal bir veri setinde saklanacak veya dashboard'da gösterilecek.

**D. Admin ve İstatistikçi için Dashboard Widget Sistemi:**
*   Dashboard, `dashboard_blocks` gibi bir tablo ile yapılandırılabilir olacak.
*   Her kullanıcı widget'ları (`Hesaplanması gereken değerler`, `Sağlanması gereken veri yığınları`, `Son Doğrulanan Veriler`, `Kural Çıktı Grafiği`) sürükleyip bırakarak düzenleyebilecek.
*   Ayarlar kullanıcıya özel `preferences` tablosunda JSON olarak saklanacak.

## **5. TAM DOSYA YAPISI VE BEKLENEN ÇIKTI**
Aşağıdaki Laravel proje yapısını oluşturun. Tüm dosyaları uygun dizinlere yerleştirin, `composer.json` ve `package.json` bağımlılıkları ekleyin.

```
ose-project/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CalculateRulesCommand.php (Zamanlanmış kural hesaplama)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── UserManagementController.php
│   │   │   ├── Statistician/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── DatasetController.php
│   │   │   │   ├── RuleController.php
│   │   │   │   └── CalculationController.php
│   │   │   ├── DataProvider/
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── DataEntryController.php
│   │   │   ├── Auth/
│   │   │   │   └── OAuthController.php (Socialite callback)
│   │   │   └── HomeController.php
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php
│   │   │   └── ...
│   │   └── Requests/
│   │       ├── StoreDataPointRequest.php
│   │       ├── StoreRuleRequest.php
│   │       └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── DataProvider.php
│   │   ├── Dataset.php
│   │   ├── DataPoint.php
│   │   ├── VerifiedData.php
│   │   ├── CalculationRule.php
│   │   └── RuleEvaluationLog.php
│   ├── Services/
│   │   ├── DataVerificationService.php (Ortalama, sigma hesaplama)
│   │   ├── RuleEvaluationService.php (DSL Parser ve Çalıştırıcı)
│   │   └── StatisticsService.php (Genel istatistik fonksiyonları)
│   ├── Jobs/
│   │   └── VerifyDataJob.php (Veri doğrulama kuyruk işi)
│   └── Providers/
│       └── AppServiceProvider.php (View Composers vs.)
├── config/
│   └── services.php (OAuth anahtarları için config)
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_core_tables.php
│   │   └── ...
│   ├── seeders/
│   │   ├── AdminUserSeeder.php
│   │   ├── SampleDatasetsSeeder.php
│   │   └── DatabaseSeeder.php
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php (AdminLTE master layout)
│   │   ├── admin/
│   │   │   └── dashboard.blade.php
│   │   ├── statistician/
│   │   │   ├── dashboard.blade.php
│   │   │   ├── datasets/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── create.blade.php
│   │   │   ├── rules/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php (DSL editörü içeren form)
│   │   │   │   └── show.blade.php (Grafikle sonuç)
│   │   │   └── calculations/
│   │   │       └── index.blade.php
│   │   ├── provider/
│   │   │   ├── dashboard.blade.php
│   │   │   └── data-entry.blade.php
│   │   └── auth/
│   │       └── login.blade.php (OAuth butonları eklenmiş)
│   └── js/
│       └── dashboard.js (Widget drag & drop)
├── routes/
│   ├── web.php (Ana route'lar)
│   ├── admin.php (Admin route grupları)
│   ├── statistician.php (İstatistikçi route grupları)
│   └── provider.php (Sağlayıcı route grupları)
├── tests/ (Feature ve Unit testler)
├── composer.json (Laravel, Socialite, ruler bağımlılıkları)
├── package.json (AdminLTE, Chart.js, Bootstrap)
└── README.md (Kurulum ve amaç dokümantasyonu)
```

## **6. ÖZEL TALİMATLAR**
1.  **Eksiksiz Kod:** Tüm dosyaları, gerçekten çalışır bir uygulama iskeleti olacak şekilde üretin. Boş `// TODO` bırakmayın, fonksiyon gövdelerini mantıklı bir şekilde doldurun.
2.  **Güvenlik:** Tüm controller'larda authorization kontrolü (`$this->authorize()`), request'lerde validation kuralları yazın.
3.  **Performans:** Veri noktası doğrulama işlemi için bir Job (`VerifyDataJob`) mutlaka oluşturun ve kuyruk (queue) kullanımını gösterin.
4.  **Yapılandırılabilirlik:** Dashboard widget'larının durumu `users` tablosuna `preferences` JSON alanı olarak kaydedilecek şekilde bir mekanizma kurun.
5.  **Kurulum Hazırlığı:** `README.md` dosyasına, `.env` ayarları (MySQL, OAuth anahtarları), `composer install`, `npm install`, `php artisan migrate --seed` adımlarını içeren tam kurulum talimatlarını yazın.
6.  **Türkçe Arayüz Desteği:** Tüm blade dosyalarındaki sabit metinler Türkçe olacak, ancak çoklu dil (localization) için hazırlıklı olacak şekilde (`__('messages.welcome')`) yapısında kodlayın.

**SON SÖZ:** Bu prompt, bir yazılım geliştirme ekibine verilebilecek teknik bir spesifikasyon dokümanının eşdeğeridir. Beklentim, bu açıklamalardan yola çıkarak, temel CRUD operasyonları, doğrulama mantığı, OAuth akışı ve DSL parser çekirdeği çalışır durumda olan, bir Git deposuna yüklenip `php artisan serve` komutu ile çalıştırılabilecek **eksiksiz bir Laravel projesi** üretmenizdir. Projenin karmaşıklığı nedeniyle kod uzun olacaktır, ancak lütfen tüm gereken dosyaları tek bir cevapta üretin.

**Başlayın.**
***
```

--------------------------------------------------------------------------------

📄 **composer.json**
```json
{
    "name": "ose-project/open-statistics-economy",
    "type": "project",
    "description": "Open Statistics Platform for Economy",
    "keywords": ["laravel", "statistics", "economics", "open-data"],
    "license": "MIT",
    "require": {
        "php": "^7.4|^8.0",
        "guzzlehttp/guzzle": "^6.5|^7.2",
        "laravel/framework": "^8.0",
        "laravel/sanctum": "^2.11",
        "laravel/socialite": "^5.2",
        "laravel/tinker": "^2.5",
        "ext-json": "*",
        "ext-mbstring": "*",
        "ext-openssl": "*",
        "ext-pdo": "*"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "laravel/sail": "^1.0",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^5.10",
        "phpunit/phpunit": "^9.5"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi"
        ]
    },
    "minimum-stability": "stable",
    "prefer-stable": true,
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "php-http/discovery": true
        }
    }
}

```

--------------------------------------------------------------------------------

📄 **htaccess.txt**
```text
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to public directory
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

<FilesMatch "\.(env|json|config.js|md|gitignore|gitattributes|lock)$">
    Order allow,deny
    Deny from all
</FilesMatch>

```

--------------------------------------------------------------------------------

📄 **package.json**
```json
{
    "private": true,
    "type": "module",
    "scripts": {
        "dev": "vite",
        "build": "vite build"
    },
    "devDependencies": {
        "@popperjs/core": "^2.11.8",
        "axios": "^1.6.1",
        "bootstrap": "^5.3.2",
        "chart.js": "^4.4.0",
        "laravel-vite-plugin": "^1.0.0",
        "vite": "^5.0.0"
    },
    "dependencies": {
        "@adminlte/adminlte": "^3.2.0",
        "admin-lte": "^3.2.0"
    }
}

```

--------------------------------------------------------------------------------

📄 **phpunit.xml**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <coverage>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
        <exclude>
            <directory>./app/Console</directory>
            <directory>./app/Exceptions</directory>
        </exclude>
        <report>
            <html outputDirectory="storage/coverage"/>
            <text outputFile="php://stdout"/>
        </report>
    </coverage>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>

```

--------------------------------------------------------------------------------

📄 **README.md**
```markdown
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

```

--------------------------------------------------------------------------------

📄 **server.php**
```php
<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';

```

--------------------------------------------------------------------------------

📄 **vite.config.js**
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['jquery', 'bootstrap', 'admin-lte'],
                    chart: ['chart.js'],
                },
            },
        },
    },
});

```

--------------------------------------------------------------------------------

📄 **webpack.mix.js**
```javascript
const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);

if (mix.inProduction()) {
    mix.version();
}

```

--------------------------------------------------------------------------------

📁 **app/**
  📁 **Console/**
    📄 **app\Console\Kernel.php**
    ```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        
        // Veri doğrulama job'larını kontrol et
        $schedule->command('queue:work --stop-when-empty')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

    ```

--------------------------------------------------------------------------------

    📁 **Commands/**
      📄 **app\Console\Commands\CalculateRulesCommand.php**
      ```php
<?php

namespace App\Console\Commands;

use App\Models\Dataset;
use App\Services\CalculationEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateRulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rules:calculate 
                            {--dataset= : Belirli bir veri seti ID}
                            {--all : Tüm veri setlerini hesapla}
                            {--force : Hata olsa da devam et}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm hesaplama kurallarını çalıştır ve sonuçları kaydet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Hesaplama kuralları çalıştırılıyor...');
        
        $datasetOption = $this->option('dataset');
        $allOption = $this->option('all');
        $forceOption = $this->option('force');
        
        $query = Dataset::whereNotNull('calculation_rule');
        
        if ($datasetOption) {
            $query->where('id', $datasetOption);
        }
        
        $datasets = $query->get();
        
        if ($datasets->isEmpty()) {
            $this->warn('Hesaplanacak veri seti bulunamadı.');
            return;
        }
        
        $this->info("{$datasets->count()} veri seti bulundu.");
        
        $calculationEngine = new CalculationEngine();
        $successCount = 0;
        $errorCount = 0;
        
        $progressBar = $this->output->createProgressBar($datasets->count());
        $progressBar->start();
        
        foreach ($datasets as $dataset) {
            try {
                $result = $calculationEngine->calculate($dataset);
                
                if ($result !== null) {
                    // Sonucu logla veya kaydet
                    $this->logCalculation($dataset, $result);
                    $successCount++;
                    
                    if ($this->getOutput()->isVerbose()) {
                        $this->line("\n[OK] {$dataset->name}: {$result}");
                    }
                } else {
                    $errorCount++;
                    $this->warn("\n[ERROR] {$dataset->name}: Hesaplanamadı");
                    
                    if (!$forceOption) {
                        $this->error('İşlem durduruldu. Devam etmek için --force kullanın.');
                        break;
                    }
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Rule calculation failed', [
                    'dataset_id' => $dataset->id,
                    'error' => $e->getMessage(),
                ]);
                
                $this->error("\n[EXCEPTION] {$dataset->name}: " . $e->getMessage());
                
                if (!$forceOption) {
                    $this->error('İşlem durduruldu. Devam etmek için --force kullanın.');
                    break;
                }
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        
        $this->newLine(2);
        $this->info('Hesaplama tamamlandı!');
        $this->table(
            ['Durum', 'Sayı'],
            [
                ['Başarılı', $successCount],
                ['Hatalı', $errorCount],
                ['Toplam', $datasets->count()],
            ]
        );
        
        if ($successCount > 0) {
            $this->info("{$successCount} veri seti başarıyla hesaplandı.");
        }
        
        if ($errorCount > 0) {
            $this->warn("{$errorCount} veri setinde hata oluştu.");
        }
    }
    
    /**
     * Hesaplama sonucunu logla
     */
    protected function logCalculation($dataset, $result)
    {
        // Hesaplama geçmişini kaydetmek için
        Log::info('Rule calculated', [
            'dataset_id' => $dataset->id,
            'dataset_name' => $dataset->name,
            'rule' => $dataset->calculation_rule,
            'result' => $result,
            'calculated_at' => now(),
        ]);
        
        // İsterseniz veritabanına da kaydedebilirsiniz
        // CalculationLog::create([...]);
    }
    
    /**
     * Command için yardım bilgisi
     */
    public function getHelp()
    {
        return <<<HELP
Hesaplama kurallarını çalıştırır.
        
Kullanım örnekleri:
  php artisan rules:calculate --all          Tüm veri setlerini hesapla
  php artisan rules:calculate --dataset=1    Belirli bir veri setini hesapla
  php artisan rules:calculate --force        Hatalarda durmadan devam et
        
Seçenekler:
  --dataset=ID    Hesaplanacak veri seti ID'si
  --all           Tüm veri setlerini hesapla
  --force         Hata olsa da devam et
HELP;
    }
}

      ```

--------------------------------------------------------------------------------

  📁 **Exceptions/**
    📄 **app\Exceptions\Handler.php**
    ```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

    ```

--------------------------------------------------------------------------------

  📁 **Http/**
    📄 **app\Http\Kernel.php**
    ```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => \App\Http\Middleware\CheckRole::class, // Bizim eklediğimiz middleware
    ];
}

    ```

--------------------------------------------------------------------------------

    📁 **Controllers/**
      📄 **app\Http\Controllers\Controller.php**
      ```php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Controllers\HomeController.php**
      ```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'statistician':
                    return redirect()->route('statistician.dashboard');
                case 'provider':
                    return redirect()->route('provider.dashboard');
            }
        }
        
        return view('welcome');
    }
}

      ```

--------------------------------------------------------------------------------

      📁 **Admin/**
        📄 **app\Http\Controllers\Admin\DashboardController.php**
        ```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_datasets' => Dataset::count(),
            'total_data_points' => DataPoint::count(),
            'verified_data_points' => DataPoint::where('is_verified', true)->count(),
            'total_providers' => DataProvider::count(),
            'verified_providers' => DataProvider::where('is_verified', true)->count(),
            'pending_verifications' => DataPoint::where('is_verified', false)->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();
        $recentDatasets = Dataset::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentDatasets'));
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Admin\DatasetController.php**
        ```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DatasetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $datasets = Dataset::with('creator')->latest()->paginate(20);
        return view('admin.datasets.index', compact('datasets'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['admin', 'statistician'])->get();
        return view('admin.datasets.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'created_by' => 'required|exists:users,id',
            'calculation_rule' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $dataset = Dataset::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'unit' => $request->unit,
            'created_by' => $request->created_by,
            'calculation_rule' => $request->calculation_rule,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('admin.datasets.index')
            ->with('success', 'Veri seti başarıyla oluşturuldu.');
    }

    public function show(Dataset $dataset)
    {
        $dataPoints = $dataset->dataPoints()
            ->with('dataProvider')
            ->orderBy('date', 'desc')
            ->paginate(20);
            
        $validationLogs = $dataset->validationLogs()
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('admin.datasets.show', compact('dataset', 'dataPoints', 'validationLogs'));
    }

    public function edit(Dataset $dataset)
    {
        $users = User::whereIn('role', ['admin', 'statistician'])->get();
        return view('admin.datasets.edit', compact('dataset', 'users'));
    }

    public function update(Request $request, Dataset $dataset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'created_by' => 'required|exists:users,id',
            'calculation_rule' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $dataset->update([
            'name' => $request->name,
            'description' => $request->description,
            'unit' => $request->unit,
            'created_by' => $request->created_by,
            'calculation_rule' => $request->calculation_rule,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('admin.datasets.index')
            ->with('success', 'Veri seti başarıyla güncellendi.');
    }

    public function destroy(Dataset $dataset)
    {
        $dataset->delete();
        return redirect()->route('admin.datasets.index')
            ->with('success', 'Veri seti başarıyla silindi.');
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Admin\UserManagementController.php**
        ```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DataProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $users = User::with('dataProvider')->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,statistician,provider',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'provider') {
            DataProvider::create([
                'user_id' => $user->id,
                'organization_name' => $request->organization_name ?? $request->name,
                'is_verified' => $request->has('verify_provider'),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,statistician,provider',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Veri sağlayıcı kaydını güncelle
        if ($request->role === 'provider') {
            $provider = $user->dataProvider ?? new DataProvider(['user_id' => $user->id]);
            $provider->organization_name = $request->organization_name ?? $user->name;
            $provider->is_verified = $request->has('verify_provider');
            $provider->save();
        } elseif ($user->dataProvider) {
            $user->dataProvider->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()
                ->with('error', 'Son admin kullanıcısını silemezsiniz.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }

    public function verifyProvider(DataProvider $provider)
    {
        $provider->update(['is_verified' => true]);
        return redirect()->back()
            ->with('success', 'Veri sağlayıcı başarıyla doğrulandı.');
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Admin\ValidationController.php**
        ```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ValidationLog;
use App\Models\Dataset;
use App\Services\DataVerificationService;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $validationLogs = ValidationLog::with('dataset')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $statusStats = [
            'pending' => ValidationLog::where('status', 'pending')->count(),
            'verified' => ValidationLog::where('status', 'verified')->count(),
            'failed' => ValidationLog::where('status', 'failed')->count(),
        ];
        
        return view('admin.validations.index', compact('validationLogs', 'statusStats'));
    }

    public function show(ValidationLog $validation)
    {
        $validation->load(['dataset', 'dataset.dataPoints' => function ($query) use ($validation) {
            $query->whereDate('date', $validation->date)
                  ->with('dataProvider');
        }]);
        
        return view('admin.validations.show', compact('validation'));
    }

    public function retry(ValidationLog $validation)
    {
        $dataset = Dataset::find($validation->dataset_id);
        
        if (!$dataset) {
            return redirect()->back()
                ->with('error', 'Veri seti bulunamadı.');
        }
        
        $service = new DataVerificationService();
        $result = $service->processValidation($dataset, $validation->date);
        
        if ($result) {
            return redirect()->back()
                ->with('success', 'Doğrulama işlemi başarıyla tekrarlandı.');
        }
        
        return redirect()->back()
            ->with('error', 'Doğrulama işlemi tekrarlanamadı.');
    }
}

        ```

--------------------------------------------------------------------------------

      📁 **Api/**
        📄 **app\Http\Controllers\Api\DataPointController.php**
        ```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataPoint;
use App\Models\Dataset;
use App\Models\DataProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataPointController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'provider') {
            return response()->json([
                'success' => false,
                'message' => 'Sadece veri sağlayıcılar veri girebilir.',
            ], 403);
        }
        
        $dataProvider = DataProvider::where('user_id', $user->id)->first();
        
        if (!$dataProvider) {
            return response()->json([
                'success' => false,
                'message' => 'Önce veri sağlayıcı profilinizi tamamlayın.',
            ], 400);
        }
        
        $request->validate([
            'dataset_id' => 'required|exists:datasets,id',
            'date' => 'required|date',
            'value' => 'required|numeric',
            'source_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);
        
        $dataset = Dataset::find($request->dataset_id);
        
        if (!$dataset->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veri seti kapalı.',
            ], 403);
        }
        
        // Check for duplicate entry
        $existing = DataPoint::where('dataset_id', $request->dataset_id)
            ->where('data_provider_id', $dataProvider->id)
            ->whereDate('date', $request->date)
            ->first();
            
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Bu tarih için zaten veri girişi yapmışsınız.',
            ], 400);
        }
        
        $dataPoint = DataPoint::create([
            'dataset_id' => $request->dataset_id,
            'data_provider_id' => $dataProvider->id,
            'date' => $request->date,
            'value' => $request->value,
            'source_url' => $request->source_url,
            'notes' => $request->notes,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Veri başarıyla eklendi.',
            'data' => $dataPoint,
        ], 201);
    }

    public function update(Request $request, DataPoint $dataPoint)
    {
        $user = Auth::user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();
        
        if (!$dataProvider || $dataPoint->data_provider_id !== $dataProvider->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veriyi güncelleme yetkiniz yok.',
            ], 403);
        }
        
        $request->validate([
            'value' => 'required|numeric',
            'source_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);
        
        $dataPoint->update([
            'value' => $request->value,
            'source_url' => $request->source_url,
            'notes' => $request->notes,
            'is_verified' => false,
            'verified_value' => null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Veri başarıyla güncellendi.',
            'data' => $dataPoint,
        ]);
    }

    public function destroy(DataPoint $dataPoint)
    {
        $user = Auth::user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();
        
        if (!$dataProvider || $dataPoint->data_provider_id !== $dataProvider->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veriyi silme yetkiniz yok.',
            ], 403);
        }
        
        $dataPoint->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Veri başarıyla silindi.',
        ]);
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Api\DatasetController.php**
        ```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index()
    {
        $datasets = Dataset::where('is_public', true)
            ->withCount('dataPoints')
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'data' => $datasets,
        ]);
    }

    public function show(Dataset $dataset)
    {
        if (!$dataset->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veri seti herkese açık değil.',
            ], 403);
        }
        
        $dataset->load(['creator', 'dataPoints' => function ($query) {
            $query->where('is_verified', true)
                  ->orderBy('date', 'desc')
                  ->limit(100);
        }]);
        
        return response()->json([
            'success' => true,
            'data' => $dataset,
        ]);
    }

    public function dataPoints(Dataset $dataset, Request $request)
    {
        if (!$dataset->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veri seti herkese açık değil.',
            ], 403);
        }
        
        $query = $dataset->dataPoints()->where('is_verified', true);
        
        // Date filter
        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        
        $dataPoints = $query->orderBy('date', $request->get('order', 'desc'))
            ->paginate($request->get('per_page', 100));
            
        return response()->json([
            'success' => true,
            'data' => $dataPoints,
        ]);
    }
}

        ```

--------------------------------------------------------------------------------

      📁 **Auth/**
        📄 **app\Http\Controllers\Auth\AuthenticatedSessionController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        
        // Role'e göre yönlendirme
        switch ($user->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'));
            case 'statistician':
                return redirect()->intended(route('statistician.dashboard'));
            case 'provider':
                return redirect()->intended(route('provider.dashboard'));
            default:
                return redirect()->intended(route('home'));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\ConfirmablePasswordController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show()
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('home'));
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\EmailVerificationNotificationController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\EmailVerificationPromptController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home'))
                    : view('auth.verify-email');
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\NewPasswordController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\OAuthController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = $this->findOrCreateUser($googleUser, 'google');
            
            Auth::login($user);
            
            return redirect()->intended($this->redirectPath());
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'error' => 'Google ile giriş yapılırken bir hata oluştu.',
            ]);
        }
    }

    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGithubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
            
            $user = $this->findOrCreateUser($githubUser, 'github');
            
            Auth::login($user);
            
            return redirect()->intended($this->redirectPath());
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'error' => 'GitHub ile giriş yapılırken bir hata oluştu.',
            ]);
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            $user = $this->findOrCreateUser($facebookUser, 'facebook');
            
            Auth::login($user);
            
            return redirect()->intended($this->redirectPath());
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'error' => 'Facebook ile giriş yapılırken bir hata oluştu.',
            ]);
        }
    }

    protected function findOrCreateUser($providerUser, $provider)
    {
        $user = User::where('email', $providerUser->getEmail())->first();

        if ($user) {
            $user->update([
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
                'avatar' => $providerUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'provider_id' => $providerUser->getId(),
                'provider_name' => $provider,
                'avatar' => $providerUser->getAvatar(),
                'password' => bcrypt(str_random(16)),
                'role' => 'provider', // Default role
            ]);
        }

        return $user;
    }

    protected function redirectPath()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                return '/admin/dashboard';
            case 'statistician':
                return '/statistician/dashboard';
            case 'provider':
                return '/provider/dashboard';
            default:
                return '/home';
        }
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\PasswordController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\PasswordResetLinkController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\RegisteredUserController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'provider', // Default role
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('provider.profile'));
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Auth\VerifyEmailController.php**
        ```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home').'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('home').'?verified=1');
    }
}

        ```

--------------------------------------------------------------------------------

      📁 **Provider/**
        📄 **app\Http\Controllers\Provider\DashboardController.php**
        ```php
<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:provider');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();

        if (!$dataProvider) {
            return redirect()->route('provider.profile')
                ->with('warning', 'Lütfen önce veri sağlayıcı profilinizi tamamlayın.');
        }

        // Bu sağlayıcının veri girebileceği veri setleri
        $availableDatasets = Dataset::where('is_public', true)
            ->with(['dataPoints' => function ($query) use ($dataProvider) {
                $query->where('data_provider_id', $dataProvider->id)
                    ->orderBy('date', 'desc')
                    ->limit(5);
            }])
            ->get();

        // Son eklenen veri noktaları
        $recentDataPoints = DataPoint::where('data_provider_id', $dataProvider->id)
            ->with('dataset')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Doğrulanmamış veri noktaları
        $pendingDataPoints = DataPoint::where('data_provider_id', $dataProvider->id)
            ->where('is_verified', false)
            ->with('dataset')
            ->count();

        // Doğrulanmış veri noktaları
        $verifiedDataPoints = DataPoint::where('data_provider_id', $dataProvider->id)
            ->where('is_verified', true)
            ->count();

        return view('provider.dashboard', compact(
            'dataProvider',
            'availableDatasets',
            'recentDataPoints',
            'pendingDataPoints',
            'verifiedDataPoints'
        ));
    }

    public function profile()
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();
        
        return view('provider.profile', compact('dataProvider'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $dataProvider = DataProvider::updateOrCreate(
            ['user_id' => $user->id],
            [
                'organization_name' => $request->organization_name,
                'website' => $request->website,
                'description' => $request->description,
            ]
        );

        return redirect()->route('provider.dashboard')
            ->with('success', 'Profil başarıyla güncellendi.');
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Provider\DataEntryController.php**
        ```php
<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use App\Services\DataVerificationService;
use Illuminate\Http\Request;

class DataEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:provider');
    }

    public function index()
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();

        if (!$dataProvider) {
            return redirect()->route('provider.profile')
                ->with('warning', 'Lütfen önce veri sağlayıcı profilinizi tamamlayın.');
        }

        $myDataPoints = DataPoint::where('data_provider_id', $dataProvider->id)
            ->with('dataset')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('provider.data-entry.index', compact('myDataPoints', 'dataProvider'));
    }

    public function create()
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();

        if (!$dataProvider) {
            return redirect()->route('provider.profile');
        }

        $datasets = Dataset::where('is_public', true)->get();
        
        return view('provider.data-entry.create', compact('datasets', 'dataProvider'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'dataset_id' => 'required|exists:datasets,id',
            'date' => 'required|date',
            'value' => 'required|numeric',
            'source_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Aynı tarih ve veri seti için daha önce veri girilmiş mi kontrol et
        $existingData = DataPoint::where('dataset_id', $request->dataset_id)
            ->where('data_provider_id', $dataProvider->id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existingData) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bu tarih için zaten veri girişi yapmışsınız. Lütfen güncelleme yapın.');
        }

        $dataPoint = DataPoint::create([
            'dataset_id' => $request->dataset_id,
            'data_provider_id' => $dataProvider->id,
            'date' => $request->date,
            'value' => $request->value,
            'source_url' => $request->source_url,
            'notes' => $request->notes,
            'is_verified' => false,
        ]);

        // Veri doğrulama servisini tetikle
        $dataset = Dataset::find($request->dataset_id);
        $service = new DataVerificationService();
        $service->checkAndTriggerValidation($dataset, $request->date);

        return redirect()->route('provider.data-entry.index')
            ->with('success', 'Veri başarıyla eklendi. Doğrulama süreci başlatıldı.');
    }

    public function edit(DataPoint $dataPoint)
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->firstOrFail();

        // Sadece kendi verilerini düzenleyebilir
        if ($dataPoint->data_provider_id !== $dataProvider->id) {
            abort(403, 'Bu veriyi düzenleme yetkiniz yok.');
        }

        $datasets = Dataset::where('is_public', true)->get();
        
        return view('provider.data-entry.edit', compact('dataPoint', 'datasets', 'dataProvider'));
    }

    public function update(Request $request, DataPoint $dataPoint)
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->firstOrFail();

        // Sadece kendi verilerini güncelleyebilir
        if ($dataPoint->data_provider_id !== $dataProvider->id) {
            abort(403, 'Bu veriyi güncelleme yetkiniz yok.');
        }

        $request->validate([
            'value' => 'required|numeric',
            'source_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldDate = $dataPoint->date;
        $oldDatasetId = $dataPoint->dataset_id;

        $dataPoint->update([
            'value' => $request->value,
            'source_url' => $request->source_url,
            'notes' => $request->notes,
            'is_verified' => false, // Güncelleme sonrası tekrar doğrulama gerekir
            'verified_value' => null,
        ]);

        // Veri doğrulama servisini tetikle (eski ve yeni veri seti/tarih için)
        $service = new DataVerificationService();
        
        if ($oldDate) {
            $oldDataset = Dataset::find($oldDatasetId);
            if ($oldDataset) {
                $service->checkAndTriggerValidation($oldDataset, $oldDate);
            }
        }

        if ($dataPoint->dataset) {
            $service->checkAndTriggerValidation($dataPoint->dataset, $dataPoint->date);
        }

        return redirect()->route('provider.data-entry.index')
            ->with('success', 'Veri başarıyla güncellendi. Doğrulama süreci başlatıldı.');
    }

    public function destroy(DataPoint $dataPoint)
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->firstOrFail();

        // Sadece kendi verilerini silebilir
        if ($dataPoint->data_provider_id !== $dataProvider->id) {
            abort(403, 'Bu veriyi silme yetkiniz yok.');
        }

        $dataset = $dataPoint->dataset;
        $date = $dataPoint->date;
        
        $dataPoint->delete();

        // Silme işleminden sonra doğrulama sürecini tekrar başlat
        $service = new DataVerificationService();
        $service->checkAndTriggerValidation($dataset, $date);

        return redirect()->route('provider.data-entry.index')
            ->with('success', 'Veri başarıyla silindi.');
    }
}

        ```

--------------------------------------------------------------------------------

      📁 **Statistician/**
        📄 **app\Http\Controllers\Statistician\CalculationController.php**
        ```php
<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Services\CalculationEngine;
use Illuminate\Http\Request;

class CalculationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:statistician');
    }

    public function index()
    {
        $user = auth()->user();
        
        $datasets = Dataset::where('created_by', $user->id)
            ->whereNotNull('calculation_rule')
            ->withCount('dataPoints')
            ->get();
            
        $calculationEngine = new CalculationEngine();
        $calculations = [];
        
        foreach ($datasets as $dataset) {
            $result = $calculationEngine->calculate($dataset);
            $calculations[] = [
                'dataset' => $dataset,
                'result' => $result,
                'formula' => $dataset->calculation_rule,
            ];
        }
        
        return view('statistician.calculations.index', compact('calculations'));
    }

    public function show(Dataset $dataset)
    {
        $this->authorize('view', $dataset);
        
        $calculationEngine = new CalculationEngine();
        $result = $calculationEngine->calculate($dataset);
        
        // Hesaplama geçmişi (son 30 gün)
        $history = $this->getCalculationHistory($dataset);
        
        return view('statistician.calculations.show', compact('dataset', 'result', 'history'));
    }

    public function runAll(Request $request)
    {
        $user = auth()->user();
        
        $datasets = Dataset::where('created_by', $user->id)
            ->whereNotNull('calculation_rule')
            ->get();
            
        $calculationEngine = new CalculationEngine();
        $results = [];
        $successCount = 0;
        
        foreach ($datasets as $dataset) {
            $result = $calculationEngine->calculate($dataset);
            if ($result !== null) {
                $successCount++;
            }
            $results[$dataset->id] = $result;
        }
        
        return redirect()->route('statistician.calculations.index')
            ->with('success', "{$successCount} veri seti başarıyla hesaplandı.");
    }

    protected function getCalculationHistory($dataset)
    {
        // Son 30 günlük veri noktalarını getir
        $dataPoints = $dataset->dataPoints()
            ->verified()
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'asc')
            ->get();
            
        $history = [];
        $calculationEngine = new CalculationEngine();
        
        // Her tarih için hesaplama yap
        $dates = $dataPoints->pluck('date')->unique();
        
        foreach ($dates as $date) {
            $tempDataset = clone $dataset;
            // Bu tarihe kadar olan verilerle hesaplama yap
            $tempData = $dataPoints->where('date', '<=', $date);
            // Basit bir ortalama hesapla (gerçekte daha kompleks olabilir)
            if ($tempData->isNotEmpty()) {
                $history[] = [
                    'date' => $date,
                    'value' => $tempData->avg('verified_value'),
                    'count' => $tempData->count(),
                ];
            }
        }
        
        return $history;
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Statistician\DashboardController.php**
        ```php
<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\ValidationLog;
use App\Services\CalculationEngine;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:statistician');
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        $myDatasets = Dataset::where('created_by', $user->id)
            ->withCount(['dataPoints', 'validationLogs'])
            ->latest()
            ->take(5)
            ->get();

        $pendingValidations = ValidationLog::where('status', 'pending')
            ->with('dataset')
            ->latest()
            ->take(5)
            ->get();

        $recentDataPoints = DataPoint::whereHas('dataset', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->with(['dataset', 'dataProvider'])
            ->latest()
            ->take(10)
            ->get();

        $calculationEngine = new CalculationEngine();
        $calculatedValues = [];
        
        foreach ($myDatasets as $dataset) {
            if ($dataset->calculation_rule) {
                $calculatedValues[$dataset->id] = [
                    'name' => $dataset->name,
                    'value' => $calculationEngine->calculate($dataset),
                    'unit' => $dataset->unit,
                ];
            }
        }

        return view('statistician.dashboard', compact(
            'myDatasets',
            'pendingValidations',
            'recentDataPoints',
            'calculatedValues'
        ));
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Statistician\DatasetController.php**
        ```php
<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Services\CalculationEngine;
use App\Services\DataVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DatasetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:statistician');
    }

    public function index()
    {
        $user = auth()->user();
        $datasets = Dataset::where('created_by', $user->id)
            ->withCount('dataPoints')
            ->latest()
            ->paginate(20);

        return view('statistician.datasets.index', compact('datasets'));
    }

    public function create()
    {
        return view('statistician.datasets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'calculation_rule' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $user = auth()->user();

        $dataset = Dataset::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'unit' => $request->unit,
            'created_by' => $user->id,
            'calculation_rule' => $request->calculation_rule,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('statistician.datasets.index')
            ->with('success', 'Veri seti başarıyla oluşturuldu.');
    }

    public function show(Dataset $dataset)
    {
        $this->authorize('view', $dataset);

        $dataPoints = $dataset->dataPoints()
            ->with('dataProvider')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $validationLogs = $dataset->validationLogs()
            ->orderBy('date', 'desc')
            ->paginate(10);

        $calculationEngine = new CalculationEngine();
        $calculatedValue = $calculationEngine->calculate($dataset);

        // Grafik için veriler
        $chartData = $this->prepareChartData($dataset);

        return view('statistician.datasets.show', compact(
            'dataset',
            'dataPoints',
            'validationLogs',
            'calculatedValue',
            'chartData'
        ));
    }

    public function edit(Dataset $dataset)
    {
        $this->authorize('update', $dataset);
        return view('statistician.datasets.edit', compact('dataset'));
    }

    public function update(Request $request, Dataset $dataset)
    {
        $this->authorize('update', $dataset);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'calculation_rule' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $dataset->update([
            'name' => $request->name,
            'description' => $request->description,
            'unit' => $request->unit,
            'calculation_rule' => $request->calculation_rule,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('statistician.datasets.index')
            ->with('success', 'Veri seti başarıyla güncellendi.');
    }

    public function destroy(Dataset $dataset)
    {
        $this->authorize('delete', $dataset);
        
        $dataset->delete();
        return redirect()->route('statistician.datasets.index')
            ->with('success', 'Veri seti başarıyla silindi.');
    }

    public function verifyData(Dataset $dataset, Request $request)
    {
        $this->authorize('update', $dataset);

        $request->validate([
            'date' => 'required|date',
        ]);

        $service = new DataVerificationService();
        $result = $service->processValidation($dataset, $request->date);

        if ($result) {
            return redirect()->back()
                ->with('success', 'Veriler başarıyla doğrulandı. Ortalama: ' . $result['average']);
        }

        return redirect()->back()
            ->with('error', 'Doğrulama için yeterli veri yok.');
    }

    protected function prepareChartData($dataset)
    {
        $verifiedData = $dataset->dataPoints()
            ->verified()
            ->select('date', 'verified_value')
            ->orderBy('date', 'asc')
            ->get();

        $labels = $verifiedData->pluck('date')->map(function ($date) {
            return $date->format('Y-m-d');
        })->toArray();

        $values = $verifiedData->pluck('verified_value')->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
            'unit' => $dataset->unit,
        ];
    }
}

        ```

--------------------------------------------------------------------------------

        📄 **app\Http\Controllers\Statistician\RuleController.php**
        ```php
<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\ValidationLog;
use App\Services\CalculationEngine;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:statistician');
    }

    public function index()
    {
        $user = auth()->user();
        
        $datasets = Dataset::where('created_by', $user->id)
            ->whereNotNull('calculation_rule')
            ->withCount('dataPoints')
            ->latest()
            ->paginate(20);

        $calculationEngine = new CalculationEngine();
        $results = [];

        foreach ($datasets as $dataset) {
            $results[$dataset->id] = $calculationEngine->calculate($dataset);
        }

        return view('statistician.rules.index', compact('datasets', 'results'));
    }

    public function create()
    {
        $user = auth()->user();
        $datasets = Dataset::where('created_by', $user->id)->get();
        
        $exampleRules = [
            'Ortalama Hesaplama' => 'ortalama(deger)',
            'Toplam ve Bölme' => 'topla(deger) / sayi',
            'Maksimum ve Minimum Fark' => '(max(deger) - min(deger)) / 2',
            'Standart Sapma Hesaplama' => 'sqrt(topla((deger - ortalama(deger))^2) / sayi)',
            'Değişim Oranı' => '(son_deger - ilk_deger) / ilk_deger * 100',
        ];

        return view('statistician.rules.create', compact('datasets', 'exampleRules'));
    }

    public function testRule(Request $request)
    {
        $request->validate([
            'dataset_id' => 'required|exists:datasets,id',
            'rule' => 'required|string',
        ]);

        $dataset = Dataset::findOrFail($request->dataset_id);
        $this->authorize('view', $dataset);

        // Geçici olarak kuralı değiştirip test et
        $originalRule = $dataset->calculation_rule;
        $dataset->calculation_rule = $request->rule;

        $calculationEngine = new CalculationEngine();
        $result = $calculationEngine->calculate($dataset);

        // Orijinal kuralı geri yükle
        $dataset->calculation_rule = $originalRule;

        return response()->json([
            'success' => true,
            'result' => $result,
            'message' => 'Kural test edildi.',
        ]);
    }

    public function calculateAll()
    {
        $user = auth()->user();
        
        $datasets = Dataset::where('created_by', $user->id)
            ->whereNotNull('calculation_rule')
            ->get();

        $calculationEngine = new CalculationEngine();
        $results = [];

        foreach ($datasets as $dataset) {
            $results[$dataset->id] = [
                'name' => $dataset->name,
                'value' => $calculationEngine->calculate($dataset),
                'unit' => $dataset->unit,
                'rule' => $dataset->calculation_rule,
            ];
        }

        return view('statistician.rules.calculations', compact('results'));
    }
}

        ```

--------------------------------------------------------------------------------

    📁 **Middleware/**
      📄 **app\Http\Middleware\Authenticate.php**
      ```php
<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\CheckRole.php**
      ```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== $role) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\EncryptCookies.php**
      ```php
<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\PreventRequestsDuringMaintenance.php**
      ```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\RedirectIfAuthenticated.php**
      ```php
<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                
                // Role'e göre yönlendirme
                switch ($user->role) {
                    case 'admin':
                        return redirect()->route('admin.dashboard');
                    case 'statistician':
                        return redirect()->route('statistician.dashboard');
                    case 'provider':
                        return redirect()->route('provider.dashboard');
                    default:
                        return redirect(RouteServiceProvider::HOME);
                }
            }
        }

        return $next($request);
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\TrimStrings.php**
      ```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\TrustHosts.php**
      ```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\TrustProxies.php**
      ```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\ValidateSignature.php**
      ```php
<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    /**
     * The names of the parameters that should be ignored.
     *
     * @var array<int, string>
     */
    protected $ignore = [
        // 'fbclid',
        // 'utm_campaign',
        // 'utm_content',
        // 'utm_medium',
        // 'utm_source',
        // 'utm_term',
    ];
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Middleware\VerifyCsrfToken.php**
      ```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}

      ```

--------------------------------------------------------------------------------

    📁 **Requests/**
      📄 **app\Http\Requests\StoreDataPointRequest.php**
      ```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDataPointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'provider';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dataset_id' => 'required|exists:datasets,id',
            'date' => 'required|date|before_or_equal:today',
            'value' => 'required|numeric|min:0|max:999999999.9999',
            'source_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'dataset_id.required' => 'Veri seti seçimi zorunludur.',
            'dataset_id.exists' => 'Seçilen veri seti geçerli değil.',
            'date.required' => 'Tarih alanı zorunludur.',
            'date.date' => 'Geçerli bir tarih giriniz.',
            'date.before_or_equal' => 'Gelecek tarihli veri giremezsiniz.',
            'value.required' => 'Değer alanı zorunludur.',
            'value.numeric' => 'Değer sayısal olmalıdır.',
            'value.min' => 'Değer sıfırdan küçük olamaz.',
            'value.max' => 'Değer çok büyük.',
            'source_url.url' => 'Geçerli bir URL giriniz.',
            'source_url.max' => 'URL çok uzun.',
            'notes.max' => 'Notlar çok uzun.',
        ];
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Requests\StoreRuleRequest.php**
      ```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'statistician';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'dataset_id' => 'required|exists:datasets,id',
            'rule_expression' => 'required|string|max:2000',
            'output_dataset_id' => 'nullable|exists:datasets,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Kural adı zorunludur.',
            'name.max' => 'Kural adı çok uzun.',
            'description.max' => 'Açıklama çok uzun.',
            'dataset_id.required' => 'Veri seti seçimi zorunludur.',
            'dataset_id.exists' => 'Seçilen veri seti geçerli değil.',
            'rule_expression.required' => 'Kural ifadesi zorunludur.',
            'rule_expression.max' => 'Kural ifadesi çok uzun.',
            'output_dataset_id.exists' => 'Çıktı veri seti geçerli değil.',
        ];
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Requests\StoreUserRequest.php**
      ```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'statistician', 'provider'])],
            'organization_name' => 'required_if:role,provider|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'İsim alanı zorunludur.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'role.required' => 'Rol seçimi zorunludur.',
            'role.in' => 'Geçersiz rol seçimi.',
            'organization_name.required_if' => 'Veri sağlayıcı için kurum adı zorunludur.',
        ];
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Requests\UpdateDatasetRequest.php**
      ```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDatasetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $dataset = $this->route('dataset');
        
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'statistician') {
            return $dataset->created_by === $user->id;
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:50',
            'calculation_rule' => 'nullable|string|max:2000',
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Veri seti adı zorunludur.',
            'name.max' => 'Veri seti adı çok uzun.',
            'description.max' => 'Açıklama çok uzun.',
            'unit.required' => 'Birim alanı zorunludur.',
            'unit.max' => 'Birim çok uzun.',
            'calculation_rule.max' => 'Hesaplama kuralı çok uzun.',
        ];
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **app\Http\Requests\UpdateUserRequest.php**
      ```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin' || $this->user()->id === $this->route('user')->id;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;
        
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'statistician', 'provider'])],
            'organization_name' => 'required_if:role,provider|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'İsim alanı zorunludur.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'role.required' => 'Rol seçimi zorunludur.',
            'role.in' => 'Geçersiz rol seçimi.',
            'organization_name.required_if' => 'Veri sağlayıcı için kurum adı zorunludur.',
        ];
    }
}

      ```

--------------------------------------------------------------------------------

  📁 **Jobs/**
    📄 **app\Jobs\ProcessValidationJob.php**
    ```php
<?php

namespace App\Jobs;

use App\Models\Dataset;
use App\Services\DataVerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessValidationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $datasetId;
    protected $date;

    public function __construct($datasetId, $date)
    {
        $this->datasetId = $datasetId;
        $this->date = $date;
    }

    public function handle()
    {
        $dataset = Dataset::find($this->datasetId);
        
        if (!$dataset) {
            return;
        }

        $service = new DataVerificationService();
        $service->processValidation($dataset, $this->date);
    }
}

    ```

--------------------------------------------------------------------------------

  📁 **Models/**
    📄 **app\Models\DataPoint.php**
    ```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataset_id',
        'data_provider_id',
        'date',
        'value',
        'source_url',
        'is_verified',
        'verified_value',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:4',
        'verified_value' => 'decimal:4',
        'is_verified' => 'boolean',
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }

    public function dataProvider()
    {
        return $this->belongsTo(DataProvider::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDataset($query, $datasetId)
    {
        return $query->where('dataset_id', $datasetId);
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Models\DataProvider.php**
    ```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_name',
        'website',
        'description',
        'trust_score',
        'is_verified',
    ];

    protected $casts = [
        'trust_score' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataPoints()
    {
        return $this->hasMany(DataPoint::class);
    }

    public function datasets()
    {
        return $this->hasManyThrough(Dataset::class, DataPoint::class);
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Models\Dataset.php**
    ```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'unit',
        'created_by',
        'calculation_rule',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dataPoints()
    {
        return $this->hasMany(DataPoint::class);
    }

    public function validationLogs()
    {
        return $this->hasMany(ValidationLog::class);
    }

    public function getVerifiedDataPoints()
    {
        return $this->dataPoints()->where('is_verified', true);
    }

    public function getLatestVerifiedValue($date = null)
    {
        $query = $this->getVerifiedDataPoints();
        
        if ($date) {
            $query->where('date', $date);
        }
        
        return $query->orderBy('date', 'desc')->first();
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Models\User.php**
    ```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'provider_id',
        'provider_name',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStatistician()
    {
        return $this->role === 'statistician';
    }

    public function isProvider()
    {
        return $this->role === 'provider';
    }

    public function dataProvider()
    {
        return $this->hasOne(DataProvider::class);
    }

    public function datasets()
    {
        return $this->hasMany(Dataset::class, 'created_by');
    }

    public function dataPoints()
    {
        return $this->hasManyThrough(DataPoint::class, DataProvider::class);
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Models\ValidationLog.php**
    ```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'dataset_id',
        'date',
        'calculated_average',
        'standard_deviation',
        'status',
        'outliers',
        'total_points',
        'valid_points',
    ];

    protected $casts = [
        'date' => 'date',
        'calculated_average' => 'decimal:4',
        'standard_deviation' => 'decimal:4',
        'outliers' => 'array',
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }
}

    ```

--------------------------------------------------------------------------------

  📁 **Policies/**
    📄 **app\Policies\DataPointPolicy.php**
    ```php
<?php

namespace App\Policies;

use App\Models\DataPoint;
use App\Models\User;
use App\Models\DataProvider;
use Illuminate\Auth\Access\Response;

class DataPointPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DataPoint $dataPoint): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'statistician') {
            return $dataPoint->dataset->created_by === $user->id;
        }

        if ($user->role === 'provider') {
            $provider = DataProvider::where('user_id', $user->id)->first();
            return $provider && $dataPoint->data_provider_id === $provider->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'provider';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DataPoint $dataPoint): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'provider') {
            $provider = DataProvider::where('user_id', $user->id)->first();
            return $provider && $dataPoint->data_provider_id === $provider->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DataPoint $dataPoint): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'provider') {
            $provider = DataProvider::where('user_id', $user->id)->first();
            return $provider && $dataPoint->data_provider_id === $provider->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DataPoint $dataPoint): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DataPoint $dataPoint): bool
    {
        return $user->role === 'admin';
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Policies\DatasetPolicy.php**
    ```php
<?php

namespace App\Policies;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DatasetPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'statistician']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dataset $dataset): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'statistician') {
            return $dataset->created_by === $user->id;
        }

        return $dataset->is_public;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'statistician']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dataset $dataset): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'statistician') {
            return $dataset->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dataset $dataset): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'statistician') {
            return $dataset->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Dataset $dataset): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Dataset $dataset): bool
    {
        return $user->role === 'admin';
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Policies\UserPolicy.php**
    ```php
<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin' || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin' || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin' && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'admin' && $user->id !== $model->id;
    }
}

    ```

--------------------------------------------------------------------------------

  📁 **Providers/**
    📄 **app\Providers\AppServiceProvider.php**
    ```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('currentUser', Auth::user());
        });
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Providers\AuthServiceProvider.php**
    ```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\User;
use App\Policies\DatasetPolicy;
use App\Policies\DataPointPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Dataset::class => DatasetPolicy::class,
        DataPoint::class => DataPointPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin gate
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        // Statistician gate
        Gate::define('statistician', function (User $user) {
            return $user->role === 'statistician';
        });

        // Provider gate
        Gate::define('provider', function (User $user) {
            return $user->role === 'provider';
        });
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Providers\BroadcastServiceProvider.php**
    ```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Providers\EventServiceProvider.php**
    ```php
<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Providers\RouteServiceProvider.php**
    ```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

    ```

--------------------------------------------------------------------------------

  📁 **Services/**
    📄 **app\Services\CalculationEngine.php**
    ```php
<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use Illuminate\Support\Facades\DB;

class CalculationEngine
{
    public function calculate($dataset)
    {
        if (!$dataset->calculation_rule) {
            return null;
        }

        $rule = $dataset->calculation_rule;
        
        // Basit DSL parser
        $result = $this->parseAndCalculate($rule, $dataset);
        
        return $result;
    }

    protected function parseAndCalculate($rule, $dataset)
    {
        // Ortalama fonksiyonu: ortalama(deger)
        if (preg_match('/ortalama\(([^)]+)\)/', $rule, $matches)) {
            return $this->calculateAverage($dataset);
        }
        
        // Toplama fonksiyonu: topla(deger)
        if (preg_match('/topla\(([^)]+)\)/', $rule, $matches)) {
            return $this->calculateSum($dataset);
        }
        
        // Bölme işlemi: topla(deger) / sayi
        if (preg_match('/topla\(([^)]+)\)\s*\/\s*([\d\.]+)/', $rule, $matches)) {
            $sum = $this->calculateSum($dataset);
            $divisor = floatval($matches[2]);
            return $divisor != 0 ? $sum / $divisor : 0;
        }
        
        // Maksimum fonksiyonu: max(deger)
        if (preg_match('/max\(([^)]+)\)/', $rule, $matches)) {
            return $this->calculateMax($dataset);
        }
        
        // Minimum fonksiyonu: min(deger)
        if (preg_match('/min\(([^)]+)\)/', $rule, $matches)) {
            return $this->calculateMin($dataset);
        }
        
        // Kompleks formül: (max(deger) - min(deger)) / 2
        if (preg_match('/\(max\(([^)]+)\)\s*-\s*min\(([^)]+)\)\)\s*\/\s*2/', $rule, $matches)) {
            $max = $this->calculateMax($dataset);
            $min = $this->calculateMin($dataset);
            return ($max - $min) / 2;
        }
        
        return null;
    }

    protected function calculateAverage($dataset)
    {
        return $dataset->getVerifiedDataPoints()
            ->select(DB::raw('AVG(verified_value) as average'))
            ->value('average');
    }

    protected function calculateSum($dataset)
    {
        return $dataset->getVerifiedDataPoints()
            ->select(DB::raw('SUM(verified_value) as total'))
            ->value('total');
    }

    protected function calculateMax($dataset)
    {
        return $dataset->getVerifiedDataPoints()
            ->select(DB::raw('MAX(verified_value) as max_value'))
            ->value('max_value');
    }

    protected function calculateMin($dataset)
    {
        return $dataset->getVerifiedDataPoints()
            ->select(DB::raw('MIN(verified_value) as min_value'))
            ->value('min_value');
    }

    protected function calculateCount($dataset)
    {
        return $dataset->getVerifiedDataPoints()->count();
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Services\DataVerificationService.php**
    ```php
<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\ValidationLog;
use App\Jobs\ProcessValidationJob;
use Illuminate\Support\Facades\DB;

class DataVerificationService
{
    public function checkAndTriggerValidation($dataset, $date)
    {
        // Aynı tarih ve veri seti için veri noktalarını say
        $dataPointsCount = DataPoint::where('dataset_id', $dataset->id)
            ->whereDate('date', $date)
            ->count();

        // Eğer 2 veya daha fazla veri noktası varsa doğrulama job'ını tetikle
        if ($dataPointsCount >= 2) {
            ProcessValidationJob::dispatch($dataset->id, $date->format('Y-m-d'));
            return true;
        }

        return false;
    }

    public function processValidation($dataset, $date)
    {
        // İlgili tarihteki tüm veri noktalarını getir
        $dataPoints = DataPoint::where('dataset_id', $dataset->id)
            ->whereDate('date', $date)
            ->get();

        if ($dataPoints->count() < 2) {
            return false;
        }

        // Değerleri array olarak al
        $values = $dataPoints->pluck('value')->toArray();

        // Ortalama ve standart sapma hesapla
        $average = $this->calculateAverage($values);
        $stdDev = $this->calculateStandardDeviation($values, $average);

        // Aykırı değerleri tespit et (ortalama ± 2*standart sapma)
        $lowerBound = $average - (2 * $stdDev);
        $upperBound = $average + (2 * $stdDev);

        $outliers = [];
        $validPoints = 0;

        foreach ($dataPoints as $dataPoint) {
            $value = $dataPoint->value;
            
            if ($value >= $lowerBound && $value <= $upperBound) {
                // Geçerli aralıkta, doğrula
                $dataPoint->update([
                    'is_verified' => true,
                    'verified_value' => $value,
                ]);
                $validPoints++;
            } else {
                // Aykırı değer
                $dataPoint->update([
                    'is_verified' => false,
                    'verified_value' => null,
                ]);
                $outliers[] = [
                    'id' => $dataPoint->id,
                    'value' => $value,
                    'provider' => $dataPoint->dataProvider->organization_name,
                ];
            }
        }

        // Doğrulama logunu kaydet
        ValidationLog::updateOrCreate(
            [
                'dataset_id' => $dataset->id,
                'date' => $date,
            ],
            [
                'calculated_average' => $average,
                'standard_deviation' => $stdDev,
                'status' => $validPoints > 0 ? 'verified' : 'failed',
                'outliers' => $outliers,
                'total_points' => $dataPoints->count(),
                'valid_points' => $validPoints,
            ]
        );

        return [
            'average' => $average,
            'std_dev' => $stdDev,
            'valid_points' => $validPoints,
            'total_points' => $dataPoints->count(),
            'outliers' => $outliers,
        ];
    }

    protected function calculateAverage(array $values)
    {
        return array_sum($values) / count($values);
    }

    protected function calculateStandardDeviation(array $values, $average = null)
    {
        if ($average === null) {
            $average = $this->calculateAverage($values);
        }

        $sumOfSquares = 0;
        foreach ($values as $value) {
            $sumOfSquares += pow($value - $average, 2);
        }

        return sqrt($sumOfSquares / count($values));
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Services\RuleEvaluationService.php**
    ```php
<?php

namespace App\Services;

use App\Models\Dataset;
use Illuminate\Support\Str;

class RuleEvaluationService
{
    protected $availableFunctions = [
        'avg', 'mean', 'sum', 'count', 'min', 'max', 'last', 'diff', 'rate', 'stddev'
    ];
    
    protected $availableOperators = ['+', '-', '*', '/', '(', ')'];
    
    /**
     * DSL ifadesini değerlendir
     */
    public function evaluate($expression, $datasetId)
    {
        // Temizle ve normalize et
        $expression = Str::lower(trim($expression));
        
        // Dataset'i yükle
        $dataset = Dataset::with(['dataPoints' => function ($query) {
            $query->where('is_verified', true)
                  ->orderBy('date', 'desc');
        }])->findOrFail($datasetId);
        
        // Dataset slug'ını değiştir
        $expression = str_replace($dataset->slug, 'dataset', $expression);
        
        // Fonksiyonları işle
        foreach ($this->availableFunctions as $function) {
            if (Str::contains($expression, $function)) {
                $expression = $this->evaluateFunction($expression, $function, $dataset);
            }
        }
        
        // Matematiksel ifadeyi değerlendir
        try {
            // Güvenli eval
            $result = $this->safeEval($expression);
            return is_numeric($result) ? (float) $result : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Fonksiyonları değerlendir
     */
    protected function evaluateFunction($expression, $function, $dataset)
    {
        $pattern = '/(' . $function . ')\(([^)]+)\)/';
        
        while (preg_match($pattern, $expression, $matches)) {
            $fullMatch = $matches[0];
            $params = $matches[2];
            
            $value = $this->callFunction($function, $params, $dataset);
            
            // Fonksiyon çağrısını değeriyle değiştir
            $expression = str_replace($fullMatch, (string) $value, $expression);
        }
        
        return $expression;
    }
    
    /**
     * Fonksiyonu çağır
     */
    protected function callFunction($function, $params, $dataset)
    {
        $dataPoints = $dataset->dataPoints;
        
        if ($dataPoints->isEmpty()) {
            return 0;
        }
        
        $values = $dataPoints->pluck('verified_value')->toArray();
        
        switch ($function) {
            case 'avg':
            case 'mean':
                return array_sum($values) / count($values);
                
            case 'sum':
                return array_sum($values);
                
            case 'count':
                return count($values);
                
            case 'min':
                return min($values);
                
            case 'max':
                return max($values);
                
            case 'last':
                $n = is_numeric($params) ? (int) $params : 1;
                $lastValues = array_slice($values, 0, $n);
                return array_sum($lastValues) / count($lastValues);
                
            case 'diff':
                if (count($values) >= 2) {
                    return end($values) - reset($values);
                }
                return 0;
                
            case 'rate':
                if (count($values) >= 2) {
                    $first = reset($values);
                    $last = end($values);
                    return $first != 0 ? (($last - $first) / $first) * 100 : 0;
                }
                return 0;
                
            case 'stddev':
                $mean = array_sum($values) / count($values);
                $sum = 0;
                foreach ($values as $value) {
                    $sum += pow($value - $mean, 2);
                }
                return sqrt($sum / count($values));
                
            default:
                return 0;
        }
    }
    
    /**
     * Güvenli matematiksel ifade değerlendirme
     */
    protected function safeEval($expression)
    {
        // Sadece sayılar, nokta, operatörler ve boşluk
        $cleanExpression = preg_replace('/[^0-9\.\+\-\*\/\(\)\s]/', '', $expression);
        
        // Boş ifade kontrolü
        if (empty(trim($cleanExpression))) {
            return 0;
        }
        
        // Matematiksel ifadeyi değerlendir
        $result = 0;
        eval('$result = ' . $cleanExpression . ';');
        
        return $result;
    }
    
    /**
     * DSL ifadesini doğrula
     */
    public function validateExpression($expression)
    {
        $errors = [];
        
        // Boş kontrolü
        if (empty(trim($expression))) {
            $errors[] = 'İfade boş olamaz.';
            return $errors;
        }
        
        // Geçersiz karakter kontrolü
        $invalidChars = preg_match('/[^a-zA-Z0-9\.\+\-\*\/\(\)_\s]/', $expression);
        if ($invalidChars) {
            $errors[] = 'İfade geçersiz karakterler içeriyor.';
        }
        
        // Parantez kontrolü
        $openParentheses = substr_count($expression, '(');
        $closeParentheses = substr_count($expression, ')');
        if ($openParentheses !== $closeParentheses) {
            $errors[] = 'Parantezler eşleşmiyor.';
        }
        
        // Fonksiyon kontrolü
        preg_match_all('/([a-z]+)\(/', $expression, $functionMatches);
        if (!empty($functionMatches[1])) {
            foreach ($functionMatches[1] as $function) {
                if (!in_array($function, $this->availableFunctions)) {
                    $errors[] = "Bilinmeyen fonksiyon: {$function}";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Örnek ifadeler getir
     */
    public function getExampleExpressions()
    {
        return [
            'Ortalama hesaplama' => 'avg(dataset)',
            'Toplam' => 'sum(dataset)',
            'Değişim oranı' => 'rate(dataset)',
            'Standart sapma' => 'stddev(dataset)',
            'Kompleks ifade' => '(max(dataset) - min(dataset)) / avg(dataset) * 100',
            'Son 5 değer ortalaması' => 'last(dataset, 5)',
            'Fark hesaplama' => 'diff(dataset)',
        ];
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **app\Services\StatisticsService.php**
    ```php
<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\User;
use App\Models\DataProvider;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Sistem geneli istatistikleri getir
     */
    public function getSystemStatistics()
    {
        return [
            'users' => [
                'total' => User::count(),
                'admin' => User::where('role', 'admin')->count(),
                'statistician' => User::where('role', 'statistician')->count(),
                'provider' => User::where('role', 'provider')->count(),
                'active_today' => User::whereDate('last_login_at', today())->count(),
            ],
            'datasets' => [
                'total' => Dataset::count(),
                'public' => Dataset::where('is_public', true)->count(),
                'private' => Dataset::where('is_public', false)->count(),
                'with_rules' => Dataset::whereNotNull('calculation_rule')->count(),
            ],
            'data' => [
                'total_points' => DataPoint::count(),
                'verified_points' => DataPoint::where('is_verified', true)->count(),
                'pending_points' => DataPoint::where('is_verified', false)->count(),
                'today_points' => DataPoint::whereDate('created_at', today())->count(),
            ],
            'providers' => [
                'total' => DataProvider::count(),
                'verified' => DataProvider::where('is_verified', true)->count(),
                'avg_trust_score' => DataProvider::avg('trust_score'),
            ],
        ];
    }

    /**
     * Dataset için istatistikleri getir
     */
    public function getDatasetStatistics($datasetId)
    {
        $dataset = Dataset::withCount(['dataPoints', 'validationLogs'])->findOrFail($datasetId);
        
        $pointsByDate = DataPoint::where('dataset_id', $datasetId)
            ->select(DB::raw('DATE(date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
            
        $pointsByProvider = DataPoint::where('dataset_id', $datasetId)
            ->join('data_providers', 'data_points.data_provider_id', '=', 'data_providers.id')
            ->select('data_providers.organization_name', DB::raw('COUNT(*) as count'))
            ->groupBy('data_providers.organization_name')
            ->orderBy('count', 'desc')
            ->get();
            
        $verificationRate = $dataset->data_points_count > 0 
            ? ($dataset->dataPoints()->where('is_verified', true)->count() / $dataset->data_points_count) * 100
            : 0;
            
        return [
            'dataset' => $dataset,
            'points_by_date' => $pointsByDate,
            'points_by_provider' => $pointsByProvider,
            'verification_rate' => round($verificationRate, 2),
            'date_range' => [
                'first' => $dataset->dataPoints()->min('date'),
                'last' => $dataset->dataPoints()->max('date'),
            ],
        ];
    }

    /**
     * Provider için istatistikleri getir
     */
    public function getProviderStatistics($providerId)
    {
        $provider = DataProvider::withCount('dataPoints')->findOrFail($providerId);
        
        $pointsByDataset = DataPoint::where('data_provider_id', $providerId)
            ->join('datasets', 'data_points.dataset_id', '=', 'datasets.id')
            ->select('datasets.name', DB::raw('COUNT(*) as count'))
            ->groupBy('datasets.name')
            ->orderBy('count', 'desc')
            ->get();
            
        $pointsByMonth = DataPoint::where('data_provider_id', $providerId)
            ->select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
            
        $verificationStats = [
            'total' => $provider->data_points_count,
            'verified' => $provider->dataPoints()->where('is_verified', true)->count(),
            'pending' => $provider->dataPoints()->where('is_verified', false)->count(),
            'rate' => $provider->data_points_count > 0 
                ? ($provider->dataPoints()->where('is_verified', true)->count() / $provider->data_points_count) * 100
                : 0,
        ];
        
        return [
            'provider' => $provider,
            'points_by_dataset' => $pointsByDataset,
            'points_by_month' => $pointsByMonth,
            'verification_stats' => $verificationStats,
        ];
    }

    /**
     * Trend analizi yap
     */
    public function analyzeTrend($datasetId, $days = 30)
    {
        $dataPoints = DataPoint::where('dataset_id', $datasetId)
            ->where('is_verified', true)
            ->where('date', '>=', now()->subDays($days))
            ->orderBy('date', 'asc')
            ->get();
            
        if ($dataPoints->count() < 2) {
            return null;
        }
        
        $values = $dataPoints->pluck('verified_value')->toArray();
        $dates = $dataPoints->pluck('date')->toArray();
        
        // Basit lineer regresyon
        $n = count($values);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
        
        foreach ($values as $i => $value) {
            $x = $i; // Zaman indeksi
            $y = $value;
            
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        // Trend yönü
        $trend = $slope > 0.01 ? 'up' : ($slope < -0.01 ? 'down' : 'stable');
        
        // Volatilite (standart sapma)
        $mean = array_sum($values) / $n;
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        $volatility = sqrt($variance / $n);
        
        return [
            'trend' => $trend,
            'slope' => $slope,
            'volatility' => $volatility,
            'mean' => $mean,
            'min' => min($values),
            'max' => max($values),
            'data_points' => $n,
            'period' => $days,
        ];
    }
}

    ```

--------------------------------------------------------------------------------

📁 **bootstrap/**
  📄 **bootstrap\app.php**
  ```php
<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;

  ```

--------------------------------------------------------------------------------

📁 **config/**
  📄 **config\app.php**
  ```php
<?php

return [

    'name' => env('APP_NAME', 'Open Statistics Economy'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    'timezone' => 'Europe/Istanbul',

    'locale' => 'tr',

    'fallback_locale' => 'en',

    'faker_locale' => 'tr_TR',

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
    ],

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    'aliases' => Facade::class([
        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'Date' => Illuminate\Support\Facades\Date::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
    ]),

];

  ```

--------------------------------------------------------------------------------

  📄 **config\auth.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    | |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password.
    |
    */

    'password_timeout' => 10800,

];

  ```

--------------------------------------------------------------------------------

  📄 **config\broadcasting.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    */

    'default' => env('BROADCAST_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\cache.php**
  ```php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "apc", "array", "database", "file",
    |         "memcached", "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, or DynamoDB cache
    | stores there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),

];

  ```

--------------------------------------------------------------------------------

  📄 **config\cors.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];

  ```

--------------------------------------------------------------------------------

  📄 **config\database.php**
  ```php
<?php

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false, // InfinityFree için strict mode kapalı
            'engine' => 'InnoDB',
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]) : [],
        ],

        // InfinityFree için SQLite fallback
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

    ],

    'migrations' => 'migrations',

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\filesystems.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\hashing.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, the bcrypt algorithm is
    | used; however, you remain free to modify this option if you wish.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Bcrypt algorithm. This will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Argon algorithm. These will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'argon' => [
        'memory' => 65536,
        'threads' => 1,
        'time' => 4,
    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\logging.php**
  ```php
<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => LOG_USER,
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\mail.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses",
    |            "postmark", "log", "array", "failover"
    |
    */

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme here. You may also configure the root paths for the packages
    | that are used to render Markdown files for your application.
    |
    */

    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\queue.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
            'after_commit' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    |
    | The following options configure the database and table that store job
    | batching information. These options can be updated to any database
    | connection and table which has been defined by your application.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\sanctum.php**
  ```php
<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production domains which access your API via a frontend SPA.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort()
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that will be checked when
    | Sanctum is trying to authenticate a request. If none of these guards
    | are able to authenticate the request, Sanctum will use the bearer
    | token that's present on an incoming request for authentication.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. If this value is null, personal access tokens do
    | not expire. This won't tweak the lifetime of first-party sessions.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum you may need to
    | customize some of the middleware Sanctum uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\services.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

];

  ```

--------------------------------------------------------------------------------

  📄 **config\session.php**
  ```php
<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default session "driver" that will be used on
    | requests. By default, we will use the lightweight native driver but
    | you may specify any of the other wonderful drivers provided here.
    |
    | Supported: "file", "cookie", "database", "apc",
    |            "memcached", "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that you wish the session
    | to be allowed to remain idle before it expires. If you want them
    | to immediately expire on the browser closing, set that option.
    |
    */

    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    |
    | This option allows you to easily specify that all of your session data
    | should be encrypted before it is stored. All encryption will be run
    | automatically by Laravel and you can use the Session like normal.
    |
    */

    'encrypt' => false,

    /*
    |--------------------------------------------------------------------------
    | Session File Location
    |--------------------------------------------------------------------------
    |
    | When using the native session driver, we need a location where session
    | files may be stored. A default has been set for you but a different
    | location may be specified. This is only needed for file sessions.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection
    |--------------------------------------------------------------------------
    |
    | When using the "database" or "redis" session drivers, you may specify a
    | connection that should be used to manage these sessions. This should
    | correspond to a connection in your database configuration options.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you may specify the table we
    | should use to manage the sessions. Of course, a sensible default is
    | provided for you; however, you are free to change this as needed.
    |
    */

    'table' => 'sessions',

    /*
    |--------------------------------------------------------------------------
    | Session Cache Store
    |--------------------------------------------------------------------------
    |
    | While using one of the framework's cache driven session backends you may
    | list a cache store that should be used for these sessions. This value
    | must match with one of the application's configured cache "stores".
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery
    |--------------------------------------------------------------------------
    |
    | Some session drivers must manually sweep their storage location to get
    | rid of old sessions from storage. Here are the chances that it will
    | happen on a given request. By default, the odds are 2 out of 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the cookie used to identify a session
    | instance by ID. The name specified here will get used every time a
    | new session cookie is created by the framework for every driver.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    |
    | The session cookie path determines the path for which the cookie will
    | be regarded as available. Typically, this will be the root path of
    | your application but you are free to change this when necessary.
    |
    */

    'path' => '/',

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    |
    | Here you may change the domain of the cookie used to identify a session
    | in your application. This will determine which domains the cookie is
    | available to in your browser. Typically, this will be your root domain.
    |
    */

    'domain' => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, session cookies will only be sent back
    | to the server if the browser has a HTTPS connection. This will keep
    | the cookie from being sent to you when it can't be done securely.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will prevent JavaScript from accessing the
    | value of the cookie and the cookie will only be accessible through
    | the HTTP protocol. You are free to modify this option if needed.
    |
    */

    'http_only' => true,

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site requests
    | take place, and can be used to mitigate CSRF attacks. By default, we
    | will set this value to "lax" since this is a secure default value.
    |
    | Supported: "lax", "strict", "none", null
    |
    */

    'same_site' => 'lax',

];

  ```

--------------------------------------------------------------------------------

  📄 **config\view.php**
  ```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];

  ```

--------------------------------------------------------------------------------

📁 **database/**
  📁 **factories/**
    📄 **database\factories\DataPointFactory.php**
    ```php
<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\DataProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataPointFactory extends Factory
{
    public function definition(): array
    {
        $value = $this->faker->randomFloat(4, 10, 1000);
        
        return [
            'dataset_id' => Dataset::factory(),
            'data_provider_id' => DataProvider::factory(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'value' => $value,
            'source_url' => $this->faker->boolean(30) ? $this->faker->url() : null,
            'is_verified' => $this->faker->boolean(60),
            'verified_value' => function (array $attributes) {
                return $attributes['is_verified'] ? $attributes['value'] : null;
            },
            'notes' => $this->faker->boolean(20) ? $this->faker->sentence() : null,
        ];
    }

    public function verified(): static
    {
        return $this->state(function (array $attributes) {
            $value = $attributes['value'] ?? $this->faker->randomFloat(4, 10, 1000);
            
            return [
                'is_verified' => true,
                'verified_value' => $value,
            ];
        });
    }

    public function unverified(): static
    {
        return $this->state([
            'is_verified' => false,
            'verified_value' => null,
        ]);
    }

    public function outlier(): static
    {
        return $this->state(function (array $attributes) {
            $baseValue = $attributes['value'] ?? $this->faker->randomFloat(4, 10, 1000);
            $outlierValue = $baseValue * $this->faker->randomElement([0.1, 0.5, 2, 5, 10]);
            
            return [
                'value' => $outlierValue,
                'is_verified' => false,
                'verified_value' => null,
            ];
        });
    }

    public function recent(): static
    {
        return $this->state([
            'date' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function forDate($date): static
    {
        return $this->state([
            'date' => $date,
        ]);
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **database\factories\DataProviderFactory.php**
    ```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataProviderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organization_name' => $this->faker->company(),
            'website' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'trust_score' => $this->faker->randomFloat(2, 50, 100),
            'is_verified' => $this->faker->boolean(70),
        ];
    }

    public function verified(): static
    {
        return $this->state([
            'is_verified' => true,
            'trust_score' => $this->faker->randomFloat(2, 80, 100),
        ]);
    }

    public function unverified(): static
    {
        return $this->state([
            'is_verified' => false,
            'trust_score' => $this->faker->randomFloat(2, 50, 79),
        ]);
    }

    public function highTrust(): static
    {
        return $this->state([
            'trust_score' => $this->faker->randomFloat(2, 90, 100),
        ]);
    }

    public function lowTrust(): static
    {
        return $this->state([
            'trust_score' => $this->faker->randomFloat(2, 50, 69),
        ]);
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **database\factories\DatasetFactory.php**
    ```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DatasetFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'description' => $this->faker->paragraph(),
            'unit' => $this->faker->randomElement(['TL', 'USD', 'EUR', 'Adet', 'Litre', 'Kg', '%']),
            'created_by' => User::factory(),
            'calculation_rule' => $this->faker->randomElement([
                'ortalama(deger)',
                'topla(deger) / sayi',
                'max(deger)',
                'min(deger)',
                '(max(deger) - min(deger)) / 2',
                null
            ]),
            'is_public' => $this->faker->boolean(80),
        ];
    }

    public function public(): static
    {
        return $this->state([
            'is_public' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state([
            'is_public' => false,
        ]);
    }

    public function withRule(): static
    {
        return $this->state([
            'calculation_rule' => $this->faker->randomElement([
                'ortalama(deger)',
                'topla(deger) / sayi',
                '(max(deger) - min(deger)) / 2',
            ]),
        ]);
    }

    public function withoutRule(): static
    {
        return $this->state([
            'calculation_rule' => null,
        ]);
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **database\factories\UserFactory.php**
    ```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => $this->faker->randomElement(['admin', 'statistician', 'provider']),
            'avatar' => $this->faker->imageUrl(100, 100, 'people'),
            'provider_id' => null,
            'provider_name' => null,
            'remember_token' => Str::random(10),
            'preferences' => json_encode(['theme' => 'light', 'language' => 'tr']),
        ];
    }

    public function admin(): static
    {
        return $this->state([
            'role' => 'admin',
        ]);
    }

    public function statistician(): static
    {
        return $this->state([
            'role' => 'statistician',
        ]);
    }

    public function provider(): static
    {
        return $this->state([
            'role' => 'provider',
        ]);
    }

    public function unverified(): static
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **database\factories\ValidationLogFactory.php**
    ```php
<?php

namespace Database\Factories;

use App\Models\Dataset;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValidationLogFactory extends Factory
{
    public function definition(): array
    {
        $totalPoints = $this->faker->numberBetween(2, 20);
        $validPoints = $this->faker->numberBetween(1, $totalPoints);
        
        return [
            'dataset_id' => Dataset::factory(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'calculated_average' => $this->faker->randomFloat(4, 10, 1000),
            'standard_deviation' => $this->faker->randomFloat(4, 0.1, 100),
            'status' => $this->faker->randomElement(['pending', 'verified', 'failed']),
            'outliers' => function (array $attributes) use ($totalPoints, $validPoints) {
                $outlierCount = $totalPoints - $validPoints;
                if ($outlierCount > 0) {
                    $outliers = [];
                    for ($i = 0; $i < $outlierCount; $i++) {
                        $outliers[] = [
                            'id' => $this->faker->randomNumber(),
                            'value' => $this->faker->randomFloat(4, 10, 1000),
                            'provider' => $this->faker->company(),
                        ];
                    }
                    return json_encode($outliers);
                }
                return null;
            },
            'total_points' => $totalPoints,
            'valid_points' => $validPoints,
        ];
    }

    public function verified(): static
    {
        return $this->state([
            'status' => 'verified',
            'valid_points' => fn($attributes) => $attributes['total_points'] ?? $this->faker->numberBetween(2, 20),
        ]);
    }

    public function pending(): static
    {
        return $this->state([
            'status' => 'pending',
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status' => 'failed',
            'valid_points' => 0,
        ]);
    }

    public function withOutliers(): static
    {
        return $this->state(function (array $attributes) {
            $totalPoints = $attributes['total_points'] ?? $this->faker->numberBetween(5, 20);
            $validPoints = $this->faker->numberBetween(1, $totalPoints - 1);
            
            $outlierCount = $totalPoints - $validPoints;
            $outliers = [];
            
            for ($i = 0; $i < $outlierCount; $i++) {
                $outliers[] = [
                    'id' => $this->faker->randomNumber(),
                    'value' => $this->faker->randomFloat(4, 10, 1000),
                    'provider' => $this->faker->company(),
                ];
            }
            
            return [
                'total_points' => $totalPoints,
                'valid_points' => $validPoints,
                'outliers' => json_encode($outliers),
                'status' => $validPoints > 0 ? 'verified' : 'failed',
            ];
        });
    }
}

    ```

--------------------------------------------------------------------------------

  📁 **migrations/**
    📄 **database\migrations\2014_10_12_000000_create_users_table.php**
    ```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('role', ['admin', 'statistician', 'provider'])->default('provider');
            $table->string('avatar')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('password')->nullable();
            $table->json('preferences')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

    ```

--------------------------------------------------------------------------------

    📄 **database\migrations\2024_01_01_000001_create_data_providers_table.php**
    ```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('organization_name');
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->decimal('trust_score', 5, 2)->default(50.00);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_providers');
    }
};

    ```

--------------------------------------------------------------------------------

    📄 **database\migrations\2024_01_01_000002_create_datasets_table.php**
    ```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('unit')->default('TL');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->text('calculation_rule')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};

    ```

--------------------------------------------------------------------------------

    📄 **database\migrations\2024_01_01_000003_create_data_points_table.php**
    ```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->onDelete('cascade');
            $table->foreignId('data_provider_id')->constrained('data_providers')->onDelete('cascade');
            $table->date('date');
            $table->decimal('value', 15, 4);
            $table->string('source_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->decimal('verified_value', 15, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['dataset_id', 'data_provider_id', 'date']);
            $table->index(['dataset_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_points');
    }
};

    ```

--------------------------------------------------------------------------------

    📄 **database\migrations\2024_01_01_000004_create_validation_logs_table.php**
    ```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('validation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('calculated_average', 15, 4);
            $table->decimal('standard_deviation', 15, 4);
            $table->enum('status', ['pending', 'verified', 'failed'])->default('pending');
            $table->json('outliers')->nullable();
            $table->integer('total_points');
            $table->integer('valid_points');
            $table->timestamps();
            
            $table->unique(['dataset_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_logs');
    }
};

    ```

--------------------------------------------------------------------------------

    📄 **database\migrations\2024_01_01_000005_create_password_reset_tokens_table.php**
    ```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};

    ```

--------------------------------------------------------------------------------

    📄 **database\migrations\2024_01_01_000006_create_sessions_table.php**
    ```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};

    ```

--------------------------------------------------------------------------------

    📄 **database\migrations\2024_01_01_000007_create_jobs_table.php**
    ```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

    ```

--------------------------------------------------------------------------------

  📁 **seeders/**
    📄 **database\seeders\AdminUserSeeder.php**
    ```php
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DataProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@ose.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Statistician User
        $statistician = User::create([
            'name' => 'Statistician User',
            'email' => 'statistician@ose.com',
            'password' => Hash::make('password'),
            'role' => 'statistician',
            'email_verified_at' => now(),
        ]);

        // Provider User
        $provider = User::create([
            'name' => 'Provider User',
            'email' => 'provider@ose.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'email_verified_at' => now(),
        ]);

        DataProvider::create([
            'user_id' => $provider->id,
            'organization_name' => 'Test Veri Sağlayıcı',
            'website' => 'https://example.com',
            'description' => 'Test veri sağlayıcı açıklaması',
            'trust_score' => 85.00,
            'is_verified' => true,
        ]);

        // Additional test providers
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Test Provider $i",
                'email' => "provider$i@ose.com",
                'password' => Hash::make('password'),
                'role' => 'provider',
                'email_verified_at' => now(),
            ]);

            DataProvider::create([
                'user_id' => $user->id,
                'organization_name' => "Test Kuruluş $i",
                'trust_score' => rand(60, 95),
                'is_verified' => rand(0, 1),
            ]);
        }
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **database\seeders\CalculationRulesSeeder.php**
    ```php
<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Database\Seeder;

class CalculationRulesSeeder extends Seeder
{
    public function run(): void
    {
        $statistician = User::where('email', 'statistician@ose.com')->first();
        
        if (!$statistician) {
            return;
        }

        $datasets = Dataset::where('created_by', $statistician->id)->get();
        
        foreach ($datasets as $dataset) {
            // Rastgele hesaplama kuralları ekle
            $rules = [
                'ortalama(deger)',
                'topla(deger) / sayi',
                'max(deger)',
                'min(deger)',
                '(max(deger) - min(deger)) / 2',
                'ortalama(deger) * 1.18',
            ];
            
            $dataset->update([
                'calculation_rule' => $this->faker->randomElement($rules),
            ]);
        }
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **database\seeders\DatabaseSeeder.php**
    ```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SampleDataSeeder::class,
            CalculationRulesSeeder::class,
        ]);
    }
}

    ```

--------------------------------------------------------------------------------

    📄 **database\seeders\SampleDatasetsSeeder.php**
    ```php
<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use App\Models\User;
use App\Models\ValidationLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ValidationLog::truncate();
        DataPoint::truncate();
        Dataset::truncate();
        DataProvider::truncate();
        User::where('id', '>', 3)->delete(); // Keep default users
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get default users
        $admin = User::where('email', 'admin@ose.com')->first();
        $statistician = User::where('email', 'statistician@ose.com')->first();
        $provider = User::where('email', 'provider@ose.com')->first();
        
        if (!$admin || !$statistician || !$provider) {
            $this->call([AdminUserSeeder::class]);
            $admin = User::where('email', 'admin@ose.com')->first();
            $statistician = User::where('email', 'statistician@ose.com')->first();
            $provider = User::where('email', 'provider@ose.com')->first();
        }

        // Create additional providers
        $providers = [$provider];
        for ($i = 1; $i <= 5; $i++) {
            $newProvider = User::create([
                'name' => "Ek Sağlayıcı $i",
                'email' => "extra_provider$i@ose.com",
                'password' => bcrypt('password'),
                'role' => 'provider',
                'email_verified_at' => now(),
            ]);

            DataProvider::create([
                'user_id' => $newProvider->id,
                'organization_name' => "Ek Kuruluş $i",
                'trust_score' => rand(60, 95),
                'is_verified' => rand(0, 1),
            ]);

            $providers[] = $newProvider;
        }

        // Create sample datasets
        $datasets = [
            [
                'name' => 'Sigara Fiyatları',
                'slug' => 'sigara-fiyatlari',
                'description' => 'Çeşitli marka sigara fiyatları (paket)',
                'unit' => 'TL',
                'created_by' => $statistician->id,
                'calculation_rule' => 'ortalama(deger)',
                'is_public' => true,
            ],
            [
                'name' => 'Ekmek Fiyatları',
                'slug' => 'ekmek-fiyatlari',
                'description' => 'Somun ekmek fiyatları',
                'unit' => 'TL',
                'created_by' => $statistician->id,
                'calculation_rule' => 'topla(deger) / sayi',
                'is_public' => true,
            ],
            [
                'name' => 'Benzin Fiyatları',
                'slug' => 'benzin-fiyatlari',
                'description' => '95 oktan benzin fiyatları',
                'unit' => 'TL/L',
                'created_by' => $statistician->id,
                'calculation_rule' => '(max(deger) - min(deger)) / 2',
                'is_public' => true,
            ],
            [
                'name' => 'Dolar Kuru',
                'slug' => 'dolar-kuru',
                'description' => 'USD/TRY döviz kuru',
                'unit' => 'TRY',
                'created_by' => $statistician->id,
                'calculation_rule' => null,
                'is_public' => true,
            ],
            [
                'name' => 'Asgari Ücret',
                'slug' => 'asgari-ucret',
                'description' => 'Net asgari ücret',
                'unit' => 'TL',
                'created_by' => $admin->id,
                'calculation_rule' => 'ortalama(deger)',
                'is_public' => false,
            ],
        ];

        $createdDatasets = [];
        foreach ($datasets as $datasetData) {
            $dataset = Dataset::create($datasetData);
            $createdDatasets[$dataset->slug] = $dataset;
        }

        // Generate sample data points for last 30 days
        $startDate = Carbon::now()->subDays(30);
        
        foreach ($createdDatasets as $slug => $dataset) {
            $baseValue = match($slug) {
                'sigara-fiyatlari' => 45.00,
                'ekmek-fiyatlari' => 12.50,
                'benzin-fiyatlari' => 38.50,
                'dolar-kuru' => 28.50,
                'asgari-ucret' => 17002.00,
                default => 100.00,
            };

            $dailyChange = match($slug) {
                'sigara-fiyatlari' => 0.02,  // ±2%
                'ekmek-fiyatlari' => 0.01,   // ±1%
                'benzin-fiyatlari' => 0.015, // ±1.5%
                'dolar-kuru' => 0.005,       // ±0.5%
                'asgari-ucret' => 0,         // No change
                default => 0.01,
            };

            // For each provider, create data points
            foreach ($providers as $providerUser) {
                $dataProvider = DataProvider::where('user_id', $providerUser->id)->first();
                
                if (!$dataProvider) continue;

                $currentValue = $baseValue * (0.9 + (rand(0, 20) / 100)); // ±10% variation between providers

                for ($day = 0; $day < 30; $day++) {
                    $date = $startDate->copy()->addDays($day);
                    
                    // Skip some days randomly (providers don't always submit data)
                    if (rand(1, 10) > 7) continue;

                    // Add daily variation
                    $variation = (rand(-100, 100) / 100) * $dailyChange;
                    $value = $currentValue * (1 + $variation);
                    
                    // Round to appropriate decimals
                    $value = round($value, $slug === 'asgari-ucret' ? 0 : 2);

                    DataPoint::create([
                        'dataset_id' => $dataset->id,
                        'data_provider_id' => $dataProvider->id,
                        'date' => $date,
                        'value' => $value,
                        'source_url' => rand(1, 10) > 7 ? 'https://example.com/source' . rand(1, 100) : null,
                        'is_verified' => rand(1, 10) > 2, // 80% verified
                        'verified_value' => rand(1, 10) > 2 ? $value : null,
                        'notes' => rand(1, 10) > 8 ? 'Sample note for ' . $date->format('Y-m-d') : null,
                        'created_at' => $date->copy()->addHours(rand(8, 18)),
                    ]);

                    $currentValue = $value;
                }
            }
        }

        // Create some validation logs
        foreach ($createdDatasets as $dataset) {
            for ($day = 0; $day < 10; $day++) {
                $date = $startDate->copy()->addDays($day * 3);
                
                $dataPoints = DataPoint::where('dataset_id', $dataset->id)
                    ->whereDate('date', $date)
                    ->get();
                
                if ($dataPoints->count() >= 2) {
                    $values = $dataPoints->pluck('value')->toArray();
                    $average = array_sum($values) / count($values);
                    
                    // Calculate standard deviation
                    $sum = 0;
                    foreach ($values as $value) {
                        $sum += pow($value - $average, 2);
                    }
                    $stdDev = sqrt($sum / count($values));
                    
                    // Determine outliers (2 sigma rule)
                    $lowerBound = $average - (2 * $stdDev);
                    $upperBound = $average + (2 * $stdDev);
                    
                    $outliers = [];
                    $validPoints = 0;
                    
                    foreach ($dataPoints as $dataPoint) {
                        if ($dataPoint->value >= $lowerBound && $dataPoint->value <= $upperBound) {
                            $validPoints++;
                        } else {
                            $outliers[] = [
                                'id' => $dataPoint->id,
                                'value' => $dataPoint->value,
                                'provider' => $dataPoint->dataProvider->organization_name,
                            ];
                        }
                    }
                    
                    ValidationLog::create([
                        'dataset_id' => $dataset->id,
                        'date' => $date,
                        'calculated_average' => $average,
                        'standard_deviation' => $stdDev,
                        'status' => $validPoints > 0 ? 'verified' : 'failed',
                        'outliers' => !empty($outliers) ? json_encode($outliers) : null,
                        'total_points' => $dataPoints->count(),
                        'valid_points' => $validPoints,
                    ]);
                }
            }
        }

        $this->command->info('Sample data created successfully!');
        $this->command->info('Default login credentials:');
        $this->command->info('Admin: admin@ose.com / password');
        $this->command->info('Statistician: statistician@ose.com / password');
        $this->command->info('Provider: provider@ose.com / password');
    }
}

    ```

--------------------------------------------------------------------------------

📁 **lang/**
  📄 **lang\tr.json**
  ```json
{
    "Dashboard": "Dashboard",
    "Welcome": "Hoş Geldiniz",
    "Login": "Giriş Yap",
    "Register": "Kayıt Ol",
    "Logout": "Çıkış Yap",
    "Email": "E-posta",
    "Password": "Şifre",
    "Remember Me": "Beni Hatırla",
    "Forgot Your Password?": "Şifrenizi mi unuttunuz?",
    "Name": "İsim",
    "Confirm Password": "Şifre Tekrar",
    "Reset Password": "Şifre Sıfırla",
    "Send Password Reset Link": "Şifre Sıfırlama Linki Gönder",
    "Verify Email Address": "E-posta Adresinizi Doğrulayın",
    "A fresh verification link has been sent to your email address.": "Yeni bir doğrulama linki e-posta adresinize gönderildi.",
    "Before proceeding, please check your email for a verification link.": "Devam etmeden önce lütfen e-posta adresinizi kontrol edin.",
    "If you did not receive the email": "E-posta almadıysanız",
    "click here to request another": "yenisini talep etmek için tıklayın"
}

  ```

--------------------------------------------------------------------------------

  📁 **tr/**
    📄 **lang\tr\auth.php**
    ```php
<?php

return [
    'failed' => 'Bu kimlik bilgileri kayıtlarımızla eşleşmiyor.',
    'password' => 'Girilen şifre yanlış.',
    'throttle' => 'Çok fazla giriş denemesi. Lütfen :seconds saniye sonra tekrar deneyin.',
];

    ```

--------------------------------------------------------------------------------

    📄 **lang\tr\pagination.php**
    ```php
<?php

return [
    'previous' => '&laquo; Önceki',
    'next' => 'Sonraki &raquo;',
];

    ```

--------------------------------------------------------------------------------

    📄 **lang\tr\passwords.php**
    ```php
<?php

return [
    'reset' => 'Şifreniz sıfırlandı!',
    'sent' => 'Şifre sıfırlama linkiniz e-posta ile gönderildi!',
    'throttled' => 'Lütfen tekrar denemeden önce bekleyin.',
    'token' => 'Bu şifre sıfırlama tokeni geçersiz.',
    'user' => "Bu e-posta adresine sahip bir kullanıcı bulunamadı.",
];

    ```

--------------------------------------------------------------------------------

    📄 **lang\tr\validation.php**
    ```php
<?php

return [
    'accepted' => ':attribute kabul edilmelidir.',
    'accepted_if' => ':attribute, :other :value olduğunda kabul edilmelidir.',
    'active_url' => ':attribute geçerli bir URL olmalıdır.',
    'after' => ':attribute, :date tarihinden sonra olmalıdır.',
    'after_or_equal' => ':attribute, :date tarihinden sonra veya aynı tarihte olmalıdır.',
    'alpha' => ':attribute sadece harflerden oluşmalıdır.',
    'alpha_dash' => ':attribute sadece harfler, rakamlar, tire ve alt çizgilerden oluşmalıdır.',
    'alpha_num' => ':attribute sadece harfler ve rakamlardan oluşmalıdır.',
    'array' => ':attribute dizi olmalıdır.',
    'before' => ':attribute, :date tarihinden önce olmalıdır.',
    'before_or_equal' => ':attribute, :date tarihinden önce veya aynı tarihte olmalıdır.',
    'between' => [
        'numeric' => ':attribute, :min ile :max arasında olmalıdır.',
        'file' => ':attribute, :min ile :max kilobayt arasında olmalıdır.',
        'string' => ':attribute, :min ile :max karakter arasında olmalıdır.',
        'array' => ':attribute, :min ile :max öğe arasında olmalıdır.',
    ],
    'boolean' => ':attribute alanı doğru veya yanlış olmalıdır.',
    'confirmed' => ':attribute onayı eşleşmiyor.',
    'current_password' => 'Şifre yanlış.',
    'date' => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals' => ':attribute, :date ile aynı tarih olmalıdır.',
    'date_format' => ':attribute, :format formatına uygun olmalıdır.',
    'declined' => ':attribute reddedilmelidir.',
    'declined_if' => ':attribute, :other :value olduğunda reddedilmelidir.',
    'different' => ':attribute ve :other farklı olmalıdır.',
    'digits' => ':attribute :digits basamaklı olmalıdır.',
    'digits_between' => ':attribute, :min ile :max basamak arasında olmalıdır.',
    'dimensions' => ':attribute geçersiz resim boyutlarına sahip.',
    'distinct' => ':attribute alanı yinelenen bir değere sahip.',
    'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
    'ends_with' => ':attribute aşağıdakilerden biriyle bitmelidir: :values.',
    'enum' => 'Seçilen :attribute geçersiz.',
    'exists' => 'Seçilen :attribute geçersiz.',
    'file' => ':attribute dosya olmalıdır.',
    'filled' => ':attribute alanı doldurulmalıdır.',
    'gt' => [
        'numeric' => ':attribute, :value değerinden büyük olmalıdır.',
        'file' => ':attribute, :value kilobayttan büyük olmalıdır.',
        'string' => ':attribute, :value karakterden fazla olmalıdır.',
        'array' => ':attribute, :value öğeden fazla olmalıdır.',
    ],
    'gte' => [
        'numeric' => ':attribute, :value değerinden büyük veya eşit olmalıdır.',
        'file' => ':attribute, :value kilobayttan büyük veya eşit olmalıdır.',
        'string' => ':attribute, :value karakterden fazla veya eşit olmalıdır.',
        'array' => ':attribute, :value öğeden fazla veya eşit olmalıdır.',
    ],
    'image' => ':attribute resim olmalıdır.',
    'in' => 'Seçilen :attribute geçersiz.',
    'in_array' => ':attribute alanı, :other içinde mevcut değil.',
    'integer' => ':attribute tam sayı olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ':attribute geçerli bir JSON dizesi olmalıdır.',
    'lt' => [
        'numeric' => ':attribute, :value değerinden küçük olmalıdır.',
        'file' => ':attribute, :value kilobayttan küçük olmalıdır.',
        'string' => ':attribute, :value karakterden az olmalıdır.',
        'array' => ':attribute, :value öğeden az olmalıdır.',
    ],
    'lte' => [
        'numeric' => ':attribute, :value değerinden küçük veya eşit olmalıdır.',
        'file' => ':attribute, :value kilobayttan küçük veya eşit olmalıdır.',
        'string' => ':attribute, :value karakterden az veya eşit olmalıdır.',
        'array' => ':attribute, :value öğeden az veya eşit olmalıdır.',
    ],
    'mac_address' => ':attribute geçerli bir MAC adresi olmalıdır.',
    'max' => [
        'numeric' => ':attribute, :max değerinden büyük olmamalıdır.',
        'file' => ':attribute, :max kilobayttan büyük olmamalıdır.',
        'string' => ':attribute, :max karakterden uzun olmamalıdır.',
        'array' => ':attribute, :max öğeden fazla olmamalıdır.',
    ],
    'mimes' => ':attribute, :values türünde bir dosya olmalıdır.',
    'mimetypes' => ':attribute, :values türünde bir dosya olmalıdır.',
    'min' => [
        'numeric' => ':attribute en az :min olmalıdır.',
        'file' => ':attribute en az :min kilobayt olmalıdır.',
        'string' => ':attribute en az :min karakter olmalıdır.',
        'array' => ':attribute en az :min öğe içermelidir.',
    ],
    'multiple_of' => ':attribute, :value değerinin katı olmalıdır.',
    'not_in' => 'Seçilen :attribute geçersiz.',
    'not_regex' => ':attribute formatı geçersiz.',
    'numeric' => ':attribute sayı olmalıdır.',
    'password' => 'Şifre yanlış.',
    'present' => ':attribute alanı mevcut olmalıdır.',
    'prohibited' => ':attribute alanı yasaklanmıştır.',
    'prohibited_if' => ':attribute alanı, :other :value olduğunda yasaklanmıştır.',
    'prohibited_unless' => ':attribute alanı, :other :values içinde olmadığı sürece yasaklanmıştır.',
    'prohibits' => ':attribute alanı :other alanının mevcut olmasını yasaklar.',
    'regex' => ':attribute formatı geçersiz.',
    'required' => ':attribute alanı gereklidir.',
    'required_array_keys' => ':attribute alanı şu anahtarları içermelidir: :values.',
    'required_if' => ':attribute alanı, :other :value olduğunda gereklidir.',
    'required_unless' => ':attribute alanı, :other :values içinde olmadığı sürece gereklidir.',
    'required_with' => ':attribute alanı, :values mevcut olduğunda gereklidir.',
    'required_with_all' => ':attribute alanı, :values mevcut olduğunda gereklidir.',
    'required_without' => ':attribute alanı, :values mevcut olmadığında gereklidir.',
    'required_without_all' => ':attribute alanı, hiçbir :values mevcut olmadığında gereklidir.',
    'same' => ':attribute ile :other eşleşmelidir.',
    'size' => [
        'numeric' => ':attribute :size olmalıdır.',
        'file' => ':attribute :size kilobayt olmalıdır.',
        'string' => ':attribute :size karakter olmalıdır.',
        'array' => ':attribute :size öğe içermelidir.',
    ],
    'starts_with' => ':attribute şunlardan biri ile başlamalıdır: :values.',
    'string' => ':attribute dize olmalıdır.',
    'timezone' => ':attribute geçerli bir saat dilimi olmalıdır.',
    'unique' => ':attribute zaten alınmış.',
    'uploaded' => ':attribute yüklenemedi.',
    'url' => ':attribute geçerli bir URL olmalıdır.',
    'uuid' => ':attribute geçerli bir UUID olmalıdır.',
    'attributes' => [
        'name' => 'İsim',
        'email' => 'E-posta',
        'password' => 'Şifre',
        'organization_name' => 'Kurum Adı',
        'website' => 'Web Sitesi',
        'description' => 'Açıklama',
        'value' => 'Değer',
        'date' => 'Tarih',
        'source_url' => 'Kaynak URL',
        'calculation_rule' => 'Hesaplama Kuralı',
        'unit' => 'Birim',
        'role' => 'Rol',
    ],
];

    ```

--------------------------------------------------------------------------------

📁 **public/**
  📄 **public\htaccess.txt**
  ```text
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# InfinityFree için ek optimizasyonlar
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

  ```

--------------------------------------------------------------------------------

  📄 **public\index.php**
  ```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);

  ```

--------------------------------------------------------------------------------

  📄 **public\robots.txt**
  ```text
User-agent: *
Allow: /

Sitemap: https://yourdomain.com/sitemap.xml

  ```

--------------------------------------------------------------------------------

📁 **resources/**
  📁 **css/**
    📄 **resources\css\app.css**
    ```css
/* Open Statistics Platform - Main Stylesheet */

:root {
    --primary-color: #3498db;
    --secondary-color: #2c3e50;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --info-color: #17a2b8;
    --light-color: #ecf0f1;
    --dark-color: #2c3e50;
}

/* Custom Styles */
.small-box {
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
}

.small-box .icon {
    font-size: 70px;
    top: -10px;
}

/* Card improvements */
.card {
    box-shadow: 0 0 15px rgba(0,0,0,0.08);
    border: none;
    border-radius: 10px;
    margin-bottom: 20px;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    background-color: #fff;
    border-radius: 10px 10px 0 0 !important;
}

.card-title {
    font-weight: 600;
    color: #2c3e50;
}

/* Button improvements */
.btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Table improvements */
.table {
    border-collapse: separate;
    border-spacing: 0;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

/* Badge improvements */
.badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
}

/* Form improvements */
.form-control {
    border-radius: 6px;
    border: 1px solid #ddd;
    padding: 10px 15px;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}

/* Alert improvements */
.alert {
    border-radius: 8px;
    border: none;
}

/* Code styling */
code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    color: #e74c3c;
    font-size: 0.9em;
}

/* Chart container */
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

/* Custom info boxes */
.info-box {
    border-radius: 8px;
    overflow: hidden;
}

.info-box-icon {
    border-radius: 8px 0 0 8px;
}

/* Navigation improvements */
.nav-pills .nav-link {
    border-radius: 6px;
    margin-bottom: 5px;
}

/* Dashboard widgets */
.callout {
    border-radius: 8px;
    border-left: 5px solid var(--primary-color);
}

.callout-info {
    border-left-color: var(--info-color);
}

.callout-success {
    border-left-color: var(--success-color);
}

.callout-warning {
    border-left-color: var(--warning-color);
}

.callout-danger {
    border-left-color: var(--danger-color);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .small-box .icon {
        font-size: 50px;
        top: 0;
    }
    
    .card-header .card-tools {
        margin-top: 10px;
        width: 100%;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd;
        box-shadow: none;
    }
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Loading spinner */
.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 20px auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Utility classes */
.cursor-pointer {
    cursor: pointer;
}

.text-underline {
    text-decoration: underline;
}

.opacity-75 {
    opacity: 0.75;
}

.opacity-50 {
    opacity: 0.5;
}

.shadow-sm {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

.shadow-md {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
}

.shadow-lg {
    box-shadow: 0 10px 15px rgba(0,0,0,0.1) !important;
}

/* Data point status colors */
.status-verified {
    color: var(--success-color);
}

.status-pending {
    color: var(--warning-color);
}

.status-failed {
    color: var(--danger-color);
}

/* Dataset card colors */
.dataset-public {
    border-left: 4px solid var(--success-color);
}

.dataset-private {
    border-left: 4px solid var(--secondary-color);
}

/* Provider trust score colors */
.trust-high {
    color: var(--success-color);
}

.trust-medium {
    color: var(--warning-color);
}

.trust-low {
    color: var(--danger-color);
}

/* Form validation states */
.is-valid {
    border-color: var(--success-color) !important;
}

.is-invalid {
    border-color: var(--danger-color) !important;
}

/* Animation for new items */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.5s ease;
}

/* Toast notification style */
.toast {
    border-radius: 8px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.toast-success {
    background-color: var(--success-color);
    color: white;
}

.toast-error {
    background-color: var(--danger-color);
    color: white;
}

.toast-warning {
    background-color: var(--warning-color);
    color: white;
}

.toast-info {
    background-color: var(--info-color);
    color: white;
}

    ```

--------------------------------------------------------------------------------

  📁 **js/**
    📄 **resources\js\app.js**
    ```javascript
/**
 * Open Statistics Platform - Main JavaScript
 */

import './bootstrap';
import 'admin-lte';
import Chart from 'chart.js/auto';

// Global variables
window.Chart = Chart;

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize all popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Form validation enhancements
    initFormValidation();

    // Table search functionality
    initTableSearch();

    // Date picker enhancements
    initDatePickers();

    // Chart initialization
    initCharts();

    // API token management
    initApiTokenManager();

    // Data export functionality
    initDataExport();
});

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });

    // Custom validation for numeric inputs
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            const min = parseFloat(this.min) || 0;
            const max = parseFloat(this.max) || Infinity;
            
            if (this.value && (value < min || value > max)) {
                this.setCustomValidity(`Değer ${min} ile ${max} arasında olmalıdır.`);
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Date validation
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (input.max) {
            input.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const maxDate = new Date(this.max);
                
                if (selectedDate > maxDate) {
                    this.setCustomValidity(`Tarih ${this.max} tarihinden sonra olamaz.`);
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });
}

/**
 * Initialize table search functionality
 */
function initTableSearch() {
    const searchInputs = document.querySelectorAll('input[name="table_search"]');
    
    searchInputs.forEach(input => {
        const table = input.closest('.card').querySelector('table');
        if (!table) return;
        
        input.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });
}

/**
 * Initialize date pickers
 */
function initDatePickers() {
    // Set max date for date inputs
    const dateInputs = document.querySelectorAll('input[type="date"]:not([max])');
    const today = new Date().toISOString().split('T')[0];
    
    dateInputs.forEach(input => {
        if (!input.max && !input.hasAttribute('data-no-max')) {
            input.max = today;
        }
    });

    // Date range pickers
    const dateRangePickers = document.querySelectorAll('.date-range-picker');
    dateRangePickers.forEach(picker => {
        const startInput = picker.querySelector('.start-date');
        const endInput = picker.querySelector('.end-date');
        
        if (startInput && endInput) {
            startInput.addEventListener('change', function() {
                endInput.min = this.value;
                if (endInput.value && new Date(endInput.value) < new Date(this.value)) {
                    endInput.value = this.value;
                }
            });
            
            endInput.addEventListener('change', function() {
                if (startInput.value && new Date(this.value) < new Date(startInput.value)) {
                    this.value = startInput.value;
                }
            });
        }
    });
}

/**
 * Initialize charts
 */
function initCharts() {
    // Dataset charts are initialized in their respective views
    // This function handles global chart configurations
    
    Chart.defaults.plugins.legend.display = true;
    Chart.defaults.plugins.legend.position = 'top';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 4;
    Chart.defaults.animation.duration = 1000;
}

/**
 * Initialize API token manager
 */
function initApiTokenManager() {
    const tokenButtons = document.querySelectorAll('.btn-api-token');
    
    tokenButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const action = this.dataset.action;
            const tokenId = this.dataset.tokenId;
            
            try {
                if (action === 'create') {
                    await createApiToken();
                } else if (action === 'revoke' && tokenId) {
                    await revokeApiToken(tokenId);
                }
            } catch (error) {
                showToast('Hata oluştu: ' + error.message, 'error');
            }
        });
    });
}

/**
 * Create new API token
 */
async function createApiToken() {
    const tokenName = prompt('API token adı girin:');
    if (!tokenName) return;
    
    try {
        const response = await fetch('/sanctum/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: tokenName })
        });
        
        const data = await response.json();
        
        if (data.token) {
            showTokenModal(data.token);
            showToast('API token başarıyla oluşturuldu!', 'success');
        } else {
            throw new Error(data.message || 'Token oluşturulamadı');
        }
    } catch (error) {
        showToast('Token oluşturma hatası: ' + error.message, 'error');
    }
}

/**
 * Show token in modal
 */
function showTokenModal(token) {
    const modal = `
        <div class="modal fade" id="tokenModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Yeni API Token</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Bu token'ı güvenli bir yere kaydedin. Bir daha gösterilmeyecek!
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" value="${token}" id="tokenInput" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToken()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    const tokenModal = new bootstrap.Modal(document.getElementById('tokenModal'));
    tokenModal.show();
    
    // Remove modal after hiding
    document.getElementById('tokenModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

/**
 * Copy token to clipboard
 */
window.copyToken = function() {
    const tokenInput = document.getElementById('tokenInput');
    tokenInput.select();
    document.execCommand('copy');
    showToast('Token panoya kopyalandı!', 'success');
};

/**
 * Revoke API token
 */
async function revokeApiToken(tokenId) {
    if (!confirm('Bu token\'ı iptal etmek istediğinize emin misiniz?')) {
        return;
    }
    
    try {
        const response = await fetch(`/sanctum/token/${tokenId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            showToast('Token başarıyla iptal edildi!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            throw new Error('Token iptal edilemedi');
        }
    } catch (error) {
        showToast('Token iptal hatası: ' + error.message, 'error');
    }
}

/**
 * Initialize data export functionality
 */
function initDataExport() {
    const exportButtons = document.querySelectorAll('.btn-export');
    
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const format = this.dataset.format || 'csv';
            const datasetId = this.dataset.datasetId;
            const startDate = this.dataset.startDate;
            const endDate = this.dataset.endDate;
            
            exportData(format, datasetId, startDate, endDate);
        });
    });
}

/**
 * Export data
 */
function exportData(format, datasetId, startDate, endDate) {
    let url = `/api/datasets/${datasetId}/export`;
    const params = new URLSearchParams();
    
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);
    params.append('format', format);
    
    url += '?' + params.toString();
    
    // Create temporary link for download
    const link = document.createElement('a');
    link.href = url;
    link.download = `dataset-${datasetId}-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="toast-body">
            ${message}
            <button type="button" class="btn-close float-end" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 3000
    });
    
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

/**
 * Format number with thousand separators
 */
window.formatNumber = function(number, decimals = 2) {
    return new Intl.NumberFormat('tr-TR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
};

/**
 * Format date
 */
window.formatDate = function(dateString, format = 'tr-TR') {
    const date = new Date(dateString);
    return date.toLocaleDateString(format);
};

/**
 * Debounce function for performance
 */
window.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Make functions available globally
window.initFormValidation = initFormValidation;
window.initTableSearch = initTableSearch;
window.initDatePickers = initDatePickers;
window.showToast = showToast;

    ```

--------------------------------------------------------------------------------

    📄 **resources\js\bootstrap.js**
    ```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add CSRF token to all requests
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Response interceptor for handling errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    window.location.href = '/login';
                    break;
                case 403:
                    showToast('Bu işlem için yetkiniz bulunmamaktadır.', 'error');
                    break;
                case 419:
                    showToast('Oturum süreniz doldu. Lütfen tekrar giriş yapın.', 'warning');
                    setTimeout(() => window.location.href = '/login', 2000);
                    break;
                case 422:
                    // Validation errors are handled in forms
                    break;
                case 500:
                    showToast('Sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.', 'error');
                    break;
            }
        }
        return Promise.reject(error);
    }
);

// Import jQuery and Bootstrap
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';

// Import FontAwesome
import '@fortawesome/fontawesome-free/js/all';

// Import AdminLTE
import 'admin-lte/dist/js/adminlte.min.js';

// Import Chart.js
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import additional plugins if needed
// import 'select2';
// import 'daterangepicker';

    ```

--------------------------------------------------------------------------------

  📁 **views/**
    📄 **resources\views\welcome.blade.php**
    ```php
@extends('layouts.app')

@section('title', 'Open Statistics for Economy')
@section('page_title', 'Açık İstatistik Platformu')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="jumbotron bg-light p-5 rounded">
                <h1 class="display-4">Open Statistics for Economy</h1>
                <p class="lead">TÜİK'in 2016 öncesi metodolojisiyle, şeffaf, çoklu kaynaktan veri toplayan ve doğrulayan açık istatistik platformu.</p>
                <hr class="my-4">
                <p>Her vatandaş kendi istatistik kurumunu kurabilir. Alternatif ekonomik göstergeler (enflasyon, maaş zammı vb.) oluşturun.</p>
                @guest
                <div class="mt-4">
                    <a class="btn btn-primary btn-lg" href="{{ route('register') }}" role="button">
                        <i class="fas fa-user-plus"></i> Hemen Kayıt Ol
                    </a>
                    <a class="btn btn-outline-primary btn-lg" href="{{ route('login') }}" role="button">
                        <i class="fas fa-sign-in-alt"></i> Giriş Yap
                    </a>
                </div>
                @endguest
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h4 class="card-title">Çoklu Rol Sistemi</h4>
                    <p class="card-text">
                        Admin, İstatistikçi ve Veri Sağlayıcı rolleri ile sistematik veri yönetimi.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calculator fa-3x text-success mb-3"></i>
                    <h4 class="card-title">DSL Hesaplama Motoru</h4>
                    <p class="card-text">
                        Basit dil ile hesaplama kuralları tanımlayın. <code>ortalama(deger)</code> gibi ifadeler kullanın.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                    <h4 class="card-title">Otomatik Veri Doğrulama</h4>
                    <p class="card-text">
                        Çoklu kaynaklardan gelen veriler otomatik olarak doğrulanır ve aykırı değerler tespit edilir.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Public Datasets -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Açık Veri Setleri</h3>
                    <div class="card-tools">
                        <a href="{{ route('api.datasets.index') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-code"></i> API
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $publicDatasets = \App\Models\Dataset::where('is_public', true)
                                ->withCount('dataPoints')
                                ->orderBy('created_at', 'desc')
                                ->take(6)
                                ->get();
                        @endphp
                        
                        @foreach($publicDatasets as $dataset)
                        <div class="col-md-4 mb-3">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $dataset->name }}</h3>
                                    <div class="card-tools">
                                        <span class="badge bg-info">{{ $dataset->data_points_count }} veri</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p>{{ Str::limit($dataset->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Birim: {{ $dataset->unit }}</small>
                                        @auth
                                            @if(Auth::user()->role === 'provider')
                                                <a href="{{ route('provider.data-entry.create') }}?dataset={{ $dataset->id }}" 
                                                   class="btn btn-primary btn-sm">
                                                    Veri Ekle
                                                </a>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer text-center">
                    @auth
                        @if(Auth::user()->role === 'statistician')
                            <a href="{{ route('statistician.datasets.index') }}" class="btn btn-primary">
                                <i class="fas fa-database"></i> Tüm Veri Setlerini Gör
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
    
    <!-- API Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-code"></i> API Erişimi</h3>
                </div>
                <div class="card-body">
                    <p>Veri sağlayıcılar API üzerinden otomatik veri girişi yapabilirler.</p>
                    <div class="alert alert-light">
                        <code>POST {{ url('/api/data-points') }}</code>
                        <pre class="mt-2">{
    "dataset_id": 1,
    "date": "2024-01-27",
    "value": 45.50,
    "source_url": "https://example.com"
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

    ```

--------------------------------------------------------------------------------

    📁 **admin/**
      📄 **resources\views\admin\dashboard.blade.php**
      ```php
@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p>Toplam Kullanıcı</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_datasets'] }}</h3>
                    <p>Veri Seti</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
                <a href="{{ route('admin.datasets.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_data_points'] }}</h3>
                    <p>Veri Noktası</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <a href="{{ route('admin.validations.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['pending_verifications'] }}</h3>
                    <p>Bekleyen Doğrulama</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('admin.validations.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Kayıt Olan Kullanıcılar</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İsim</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Kayıt Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'statistician' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Recent Datasets -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Eklenen Veri Setleri</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İsim</th>
                                <th>Oluşturan</th>
                                <th>Tarih</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDatasets as $dataset)
                            <tr>
                                <td>{{ $dataset->id }}</td>
                                <td>{{ $dataset->name }}</td>
                                <td>{{ $dataset->creator->name ?? '-' }}</td>
                                <td>{{ $dataset->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $dataset->is_public ? 'success' : 'secondary' }}">
                                        {{ $dataset->is_public ? 'Açık' : 'Kapalı' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Stats -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sistem İstatistikleri</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doğrulanmış Veri</span>
                                    <span class="info-box-number">{{ $stats['verified_data_points'] }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-building"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Veri Sağlayıcı</span>
                                    <span class="info-box-number">{{ $stats['total_providers'] }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-shield-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doğrulanmış Sağlayıcı</span>
                                    <span class="info-box-number">{{ $stats['verified_providers'] }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-chart-pie"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doğrulama Oranı</span>
                                    <span class="info-box-number">
                                        @if($stats['total_data_points'] > 0)
                                            {{ round(($stats['verified_data_points'] / $stats['total_data_points']) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

      ```

--------------------------------------------------------------------------------

      📁 **datasets/**
        📄 **resources\views\admin\datasets\create.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Yeni Veri Seti')
@section('page_title', 'Yeni Veri Seti Oluştur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item active">Yeni Oluştur</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                </div>
                <form action="{{ route('admin.datasets.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Veri Seti Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unit">Birim *</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                   id="unit" name="unit" value="{{ old('unit', 'TL') }}" required>
                            @error('unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Örnek: TL, USD, Adet, Litre, % vb.</small>
                        </div>

                        <div class="form-group">
                            <label for="created_by">Oluşturan *</label>
                            <select class="form-control @error('created_by') is-invalid @enderror" 
                                    id="created_by" name="created_by" required>
                                <option value="">Seçiniz</option>
                                @foreach($users as $user)
                                    @if(in_array($user->role, ['admin', 'statistician']))
                                        <option value="{{ $user->id }}" 
                                                {{ old('created_by') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('created_by')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="calculation_rule">Hesaplama Kuralı (DSL)</label>
                            <textarea class="form-control @error('calculation_rule') is-invalid @enderror" 
                                      id="calculation_rule" name="calculation_rule" rows="4" 
                                      placeholder="Örnek: ortalama(deger), topla(deger) / sayi">{{ old('calculation_rule') }}</textarea>
                            @error('calculation_rule')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                <strong>Kullanılabilir fonksiyonlar:</strong><br>
                                • ortalama(deger) veya mean(deger) - Ortalama hesaplama<br>
                                • topla(deger) veya sum(deger) - Toplam hesaplama<br>
                                • max(deger) - Maksimum değer<br>
                                • min(deger) - Minimum değer<br>
                                • sayi veya count(deger) - Veri sayısı<br>
                                • stddev(deger) - Standart sapma
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="is_public" name="is_public" value="1" checked>
                                <label class="custom-control-label" for="is_public">
                                    Veri seti herkese açık olsun
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Açık veri setlerine tüm veri sağlayıcılar veri girebilir.
                                Kapalı veri setlerine sadece oluşturan erişebilir.
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="{{ route('admin.datasets.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\admin\datasets\edit.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Veri Seti Düzenle')
@section('page_title', 'Veri Seti Düzenle: ' . $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.datasets.show', $dataset) }}">{{ $dataset->name }}</a></li>
    <li class="breadcrumb-item active">Düzenle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Düzenle</h3>
                </div>
                <form action="{{ route('admin.datasets.update', $dataset) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Veri Seti Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $dataset->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $dataset->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unit">Birim *</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                   id="unit" name="unit" value="{{ old('unit', $dataset->unit) }}" required>
                            @error('unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="created_by">Oluşturan *</label>
                            <select class="form-control @error('created_by') is-invalid @enderror" 
                                    id="created_by" name="created_by" required>
                                @foreach($users as $user)
                                    @if(in_array($user->role, ['admin', 'statistician']))
                                        <option value="{{ $user->id }}" 
                                                {{ old('created_by', $dataset->created_by) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('created_by')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="calculation_rule">Hesaplama Kuralı (DSL)</label>
                            <textarea class="form-control @error('calculation_rule') is-invalid @enderror" 
                                      id="calculation_rule" name="calculation_rule" rows="4">{{ old('calculation_rule', $dataset->calculation_rule) }}</textarea>
                            @error('calculation_rule')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Kullanılabilir fonksiyonlar: ortalama(), topla(), max(), min(), sayi, stddev()
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="is_public" name="is_public" value="1" 
                                       {{ old('is_public', $dataset->is_public) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_public">
                                    Veri seti herkese açık olsun
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.datasets.show', $dataset) }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\admin\datasets\index.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Veri Setleri')
@section('page_title', 'Veri Setleri Yönetimi')

@section('breadcrumb')
    <li class="breadcrumb-item active">Veri Setleri</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="{{ route('admin.datasets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Veri Seti
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veri Setleri</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Ara...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İsim</th>
                                <th>Oluşturan</th>
                                <th>Veri Noktası</th>
                                <th>Birim</th>
                                <th>Durum</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datasets as $dataset)
                            <tr>
                                <td>{{ $dataset->id }}</td>
                                <td>
                                    <a href="{{ route('admin.datasets.show', $dataset) }}">
                                        {{ $dataset->name }}
                                    </a>
                                    @if($dataset->calculation_rule)
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calculator"></i> Hesaplama kuralı var
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $dataset->creator->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $dataset->data_points_count ?? 0 }}</span>
                                </td>
                                <td>{{ $dataset->unit }}</td>
                                <td>
                                    @if($dataset->is_public)
                                        <span class="badge bg-success">Açık</span>
                                    @else
                                        <span class="badge bg-warning">Kapalı</span>
                                    @endif
                                </td>
                                <td>{{ $dataset->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.datasets.show', $dataset) }}" 
                                           class="btn btn-sm btn-info" title="Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.datasets.edit', $dataset) }}" 
                                           class="btn btn-sm btn-warning" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.datasets.destroy', $dataset) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Bu veri setini silmek istediğinizden emin misiniz?')"
                                                    title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $datasets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('input[name="table_search"]').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\admin\datasets\show.blade.php**
        ```php
@extends('layouts.app')

@section('title', $dataset->name)
@section('page_title', $dataset->name . ' - Detaylar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item active">Görüntüle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Dataset Info -->
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.datasets.edit', $dataset) }}" class="btn btn-tool">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>İsim:</dt>
                        <dd>{{ $dataset->name }}</dd>
                        
                        <dt>Slug:</dt>
                        <dd><code>{{ $dataset->slug }}</code></dd>
                        
                        <dt>Açıklama:</dt>
                        <dd>{{ $dataset->description ?? 'Belirtilmemiş' }}</dd>
                        
                        <dt>Birim:</dt>
                        <dd>{{ $dataset->unit }}</dd>
                        
                        <dt>Oluşturan:</dt>
                        <dd>
                            {{ $dataset->creator->name }}
                            <small class="text-muted">({{ $dataset->creator->email }})</small>
                        </dd>
                        
                        <dt>Hesaplama Kuralı:</dt>
                        <dd>
                            @if($dataset->calculation_rule)
                                <code>{{ $dataset->calculation_rule }}</code>
                            @else
                                <span class="text-muted">Tanımlanmamış</span>
                            @endif
                        </dd>
                        
                        <dt>Durum:</dt>
                        <dd>
                            @if($dataset->is_public)
                                <span class="badge bg-success">Açık</span>
                                <small class="text-muted">(Tüm sağlayıcılar erişebilir)</small>
                            @else
                                <span class="badge bg-warning">Kapalı</span>
                                <small class="text-muted">(Sadece oluşturan erişebilir)</small>
                            @endif
                        </dd>
                        
                        <dt>Oluşturulma:</dt>
                        <dd>{{ $dataset->created_at->format('d.m.Y H:i') }}</dd>
                        
                        <dt>Son Güncelleme:</dt>
                        <dd>{{ $dataset->updated_at->format('d.m.Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card card-info mt-3">
                <div class="card-header">
                    <h3 class="card-title">İstatistikler</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Veri</span>
                                    <span class="info-box-number">{{ $dataset->dataPoints->count() }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Doğrulanmış</span>
                                    <span class="info-box-number">
                                        {{ $dataset->dataPoints->where('is_verified', true)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bekleyen</span>
                                    <span class="info-box-number">
                                        {{ $dataset->dataPoints->where('is_verified', false)->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Sağlayıcı</span>
                                    <span class="info-box-number">
                                        {{ $dataset->dataPoints->groupBy('data_provider_id')->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart and Data -->
        <div class="col-md-8">
            <!-- Chart -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Doğrulanmış Veri Grafiği</h3>
                </div>
                <div class="card-body">
                    <canvas id="datasetChart" height="250"></canvas>
                </div>
            </div>

            <!-- Data Points -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Veri Noktaları</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Sağlayıcı</th>
                                    <th>Tarih</th>
                                    <th>Değer</th>
                                    <th>Doğrulanmış</th>
                                    <th>Kaynak</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataPoints as $dataPoint)
                                <tr>
                                    <td>{{ $dataPoint->dataProvider->organization_name }}</td>
                                    <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                    <td>
                                        <strong>{{ number_format($dataPoint->value, 4) }}</strong>
                                        {{ $dataset->unit }}
                                    </td>
                                    <td>
                                        @if($dataPoint->verified_value)
                                            {{ number_format($dataPoint->verified_value, 4) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dataPoint->source_url)
                                            <a href="{{ $dataPoint->source_url }}" target="_blank">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dataPoint->is_verified)
                                            <span class="badge bg-success">Doğrulandı</span>
                                        @else
                                            <span class="badge bg-warning">Bekliyor</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    {{ $dataPoints->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Logs -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Doğrulama Geçmişi</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Ortalama</th>
                                <th>Standart Sapma</th>
                                <th>Toplam Veri</th>
                                <th>Geçerli Veri</th>
                                <th>Durum</th>
                                <th>İşlem Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validationLogs as $log)
                            <tr>
                                <td>{{ $log->date->format('d.m.Y') }}</td>
                                <td>{{ number_format($log->calculated_average, 4) }}</td>
                                <td>{{ number_format($log->standard_deviation, 4) }}</td>
                                <td>{{ $log->total_points }}</td>
                                <td>{{ $log->valid_points }}</td>
                                <td>
                                    @if($log->status == 'verified')
                                        <span class="badge bg-success">Doğrulandı</span>
                                    @elseif($log->status == 'failed')
                                        <span class="badge bg-danger">Başarısız</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $validationLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('datasetChart').getContext('2d');
        
        // Chart verilerini backend'den al (şimdilik mock data)
        var verifiedData = @json($dataset->dataPoints->where('is_verified', true)->sortBy('date')->values());
        
        var labels = verifiedData.map(function(item) {
            return new Date(item.date).toLocaleDateString('tr-TR');
        });
        
        var values = verifiedData.map(function(item) {
            return parseFloat(item.verified_value || item.value);
        });
        
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ $dataset->name }} ({{ $dataset->unit }})',
                    data: values,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(4);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tarih'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: '{{ $dataset->unit }}'
                        },
                        beginAtZero: false
                    }
                }
            }
        });
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

      📁 **users/**
        📄 **resources\views\admin\users\create.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Yeni Kullanıcı')
@section('page_title', 'Yeni Kullanıcı Oluştur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Kullanıcılar</a></li>
    <li class="breadcrumb-item active">Yeni Kullanıcı</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Kullanıcı Bilgileri</h3>
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
                            <label for="email">Email Adresi *</label>
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
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="statistician" {{ old('role') == 'statistician' ? 'selected' : '' }}>İstatistikçi</option>
                                <option value="provider" {{ old('role') == 'provider' ? 'selected' : '' }}>Veri Sağlayıcı</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div id="providerFields" style="display: none;">
                            <div class="form-group">
                                <label for="organization_name">Kurum Adı *</label>
                                <input type="text" class="form-control @error('organization_name') is-invalid @enderror" 
                                       id="organization_name" name="organization_name" value="{{ old('organization_name') }}">
                                @error('organization_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" 
                                       id="verify_provider" name="verify_provider" value="1" {{ old('verify_provider') ? 'checked' : '' }}>
                                <label class="form-check-label" for="verify_provider">Veri sağlayıcıyı doğrula</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Kullanıcı Oluştur</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default">İptal</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Rol Açıklamaları</h3>
                </div>
                <div class="card-body">
                    <h5><span class="badge bg-danger">Admin</span></h5>
                    <p>Tüm sistem yönetimi, kullanıcı onayı, veri setleri ve doğrulama işlemleri.</p>
                    
                    <h5><span class="badge bg-warning">İstatistikçi</span></h5>
                    <p>Veri seti oluşturma, hesaplama kuralları tanımlama, tüm verileri görüntüleme.</p>
                    
                    <h5><span class="badge bg-primary">Veri Sağlayıcı</span></h5>
                    <p>Sadece kendine atanmış veri setlerine veri girişi yapabilir.</p>
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
        
        // Sayfa yüklendiğinde kontrol et
        $('#role').trigger('change');
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\admin\users\edit.blade.php**
        ```php
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

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\admin\users\index.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi')
@section('page_title', 'Kullanıcı Yönetimi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Kullanıcılar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Kullanıcılar</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Kullanıcı
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İsim</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Veri Sağlayıcı</th>
                                <th>Kayıt Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar }}" class="img-circle elevation-2" alt="User Avatar" style="width: 30px; height: 30px; margin-right: 10px;">
                                        @else
                                            <div class="img-circle elevation-2 bg-primary d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; margin-right: 10px;">
                                                <span class="text-white">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'statistician' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->dataProvider)
                                        @if($user->dataProvider->is_verified)
                                            <span class="badge bg-success">Doğrulanmış</span>
                                        @else
                                            <span class="badge bg-warning">Bekliyor</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Yok</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

      📁 **validations/**
        📄 **resources\views\admin\validations\index.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Doğrulama Geçmişi')
@section('page_title', 'Doğrulama Geçmişi')

@section('breadcrumb')
    <li class="breadcrumb-item active">Doğrulamalar</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-history"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Toplam Doğrulama</span>
                    <span class="info-box-number">{{ $validationLogs->total() }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Doğrulanmış</span>
                    <span class="info-box-number">{{ $statusStats['verified'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bekleyen</span>
                    <span class="info-box-number">{{ $statusStats['pending'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Başarısız</span>
                    <span class="info-box-number">{{ $statusStats['failed'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Doğrulama Kayıtları</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Ara...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Veri Seti</th>
                                <th>Tarih</th>
                                <th>Ortalama</th>
                                <th>Toplam Veri</th>
                                <th>Geçerli Veri</th>
                                <th>Durum</th>
                                <th>İşlem Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validationLogs as $validation)
                            <tr>
                                <td>{{ $validation->id }}</td>
                                <td>
                                    <a href="{{ route('admin.datasets.show', $validation->dataset) }}">
                                        {{ $validation->dataset->name }}
                                    </a>
                                </td>
                                <td>{{ $validation->date->format('d.m.Y') }}</td>
                                <td>
                                    @if($validation->calculated_average)
                                        {{ number_format($validation->calculated_average, 4) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $validation->total_points }}</td>
                                <td>
                                    <span class="{{ $validation->valid_points == $validation->total_points ? 'text-success' : 'text-warning' }}">
                                        {{ $validation->valid_points }}
                                    </span>
                                </td>
                                <td>
                                    @if($validation->status == 'verified')
                                        <span class="badge bg-success">Doğrulandı</span>
                                    @elseif($validation->status == 'failed')
                                        <span class="badge bg-danger">Başarısız</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>{{ $validation->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.validations.show', $validation) }}" 
                                           class="btn btn-sm btn-info" title="Detaylar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($validation->status != 'verified')
                                        <form action="{{ route('admin.validations.retry', $validation) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" 
                                                    onclick="return confirm('Bu doğrulama işlemini tekrar başlatmak istediğinizden emin misiniz?')"
                                                    title="Tekrar Dene">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $validationLogs->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Outliers -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Son Aykırı Değerler</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Veri Seti</th>
                                    <th>Tarih</th>
                                    <th>Sağlayıcı</th>
                                    <th>Girilen Değer</th>
                                    <th>Ortalama</th>
                                    <th>Standart Sapma</th>
                                    <th>Fark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentOutliers = [];
                                    foreach($validationLogs as $validation) {
                                        if ($validation->outliers && $validation->status != 'verified') {
                                            foreach(json_decode($validation->outliers, true) as $outlier) {
                                                $recentOutliers[] = [
                                                    'dataset' => $validation->dataset->name,
                                                    'date' => $validation->date->format('d.m.Y'),
                                                    'provider' => $outlier['provider'] ?? 'Bilinmiyor',
                                                    'value' => $outlier['value'] ?? 0,
                                                    'average' => $validation->calculated_average,
                                                    'stddev' => $validation->standard_deviation,
                                                ];
                                            }
                                        }
                                    }
                                    $recentOutliers = array_slice($recentOutliers, 0, 10);
                                @endphp
                                
                                @if(count($recentOutliers) > 0)
                                    @foreach($recentOutliers as $outlier)
                                    <tr>
                                        <td>{{ $outlier['dataset'] }}</td>
                                        <td>{{ $outlier['date'] }}</td>
                                        <td>{{ $outlier['provider'] }}</td>
                                        <td>{{ number_format($outlier['value'], 4) }}</td>
                                        <td>{{ number_format($outlier['average'], 4) }}</td>
                                        <td>{{ number_format($outlier['stddev'], 4) }}</td>
                                        <td>
                                            @php
                                                $diff = abs($outlier['value'] - $outlier['average']);
                                                $diffPercent = $outlier['average'] > 0 ? ($diff / $outlier['average']) * 100 : 0;
                                            @endphp
                                            <span class="text-danger">
                                                %{{ number_format($diffPercent, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-success m-0">
                                                Son 24 saatte aykırı değer bulunamadı.
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('input[name="table_search"]').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\admin\validations\show.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Doğrulama Detayları')
@section('page_title', 'Doğrulama Detayları')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.validations.index') }}">Doğrulamalar</a></li>
    <li class="breadcrumb-item active">Detaylar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Validation Info -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Doğrulama Bilgileri</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">{{ $validation->id }}</dd>
                        
                        <dt class="col-sm-4">Veri Seti:</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.datasets.show', $validation->dataset) }}">
                                {{ $validation->dataset->name }}
                            </a>
                        </dd>
                        
                        <dt class="col-sm-4">Tarih:</dt>
                        <dd class="col-sm-8">{{ $validation->date->format('d.m.Y') }}</dd>
                        
                        <dt class="col-sm-4">Durum:</dt>
                        <dd class="col-sm-8">
                            @if($validation->status == 'verified')
                                <span class="badge bg-success">Doğrulandı</span>
                            @elseif($validation->status == 'failed')
                                <span class="badge bg-danger">Başarısız</span>
                            @else
                                <span class="badge bg-warning">Bekliyor</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-4">Ortalama:</dt>
                        <dd class="col-sm-8">{{ number_format($validation->calculated_average, 4) }}</dd>
                        
                        <dt class="col-sm-4">Standart Sapma:</dt>
                        <dd class="col-sm-8">{{ number_format($validation->standard_deviation, 4) }}</dd>
                        
                        <dt class="col-sm-4">Toplam Veri:</dt>
                        <dd class="col-sm-8">{{ $validation->total_points }}</dd>
                        
                        <dt class="col-sm-4">Geçerli Veri:</dt>
                        <dd class="col-sm-8">
                            <span class="{{ $validation->valid_points == $validation->total_points ? 'text-success' : 'text-warning' }}">
                                {{ $validation->valid_points }}
                            </span>
                            ({{ $validation->total_points > 0 ? round(($validation->valid_points / $validation->total_points) * 100, 2) : 0 }}%)
                        </dd>
                        
                        <dt class="col-sm-4">Aykırı Değer:</dt>
                        <dd class="col-sm-8">
                            {{ $validation->total_points - $validation->valid_points }}
                        </dd>
                        
                        <dt class="col-sm-4">İşlem Tarihi:</dt>
                        <dd class="col-sm-8">{{ $validation->created_at->format('d.m.Y H:i') }}</dd>
                    </dl>
                    
                    @if($validation->status != 'verified')
                    <div class="mt-3">
                        <form action="{{ route('admin.validations.retry', $validation) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block" 
                                    onclick="return confirm('Bu doğrulama işlemini tekrar başlatmak istediğinizden emin misiniz?')">
                                <i class="fas fa-redo"></i> Doğrulamayı Tekrar Başlat
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Range Info -->
            <div class="card card-info mt-3">
                <div class="card-header">
                    <h3 class="card-title">Doğrulama Aralığı</h3>
                </div>
                <div class="card-body">
                    @php
                        $lowerBound = $validation->calculated_average - (2 * $validation->standard_deviation);
                        $upperBound = $validation->calculated_average + (2 * $validation->standard_deviation);
                    @endphp
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> 3 Sigma Kuralı</h5>
                        <p class="mb-0">
                            Doğrulama için kullanılan aralık: 
                            <strong>Ortalama ± 2×Standart Sapma</strong><br>
                            Geçerli aralık: <strong>{{ number_format($lowerBound, 4) }} - {{ number_format($upperBound, 4) }}</strong>
                        </p>
                    </div>
                    
                    <div class="progress" style="height: 30px;">
                        <div class="progress-bar bg-success" style="width: {{ ($validation->valid_points / $validation->total_points) * 100 }}%">
                            {{ $validation->valid_points }} Geçerli
                        </div>
                        <div class="progress-bar bg-danger" style="width: {{ (($validation->total_points - $validation->valid_points) / $validation->total_points) * 100 }}%">
                            {{ $validation->total_points - $validation->valid_points }} Aykırı
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Points -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">İlgili Veri Noktaları</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sağlayıcı</th>
                                    <th>Değer</th>
                                    <th>Doğrulanmış</th>
                                    <th>Durum</th>
                                    <th>Fark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dataPoints = $validation->dataset->dataPoints
                                        ->where('date', $validation->date)
                                        ->sortByDesc('value');
                                @endphp
                                
                                @foreach($dataPoints as $dataPoint)
                                @php
                                    $diff = $dataPoint->value - $validation->calculated_average;
                                    $diffPercent = $validation->calculated_average > 0 
                                        ? abs($diff / $validation->calculated_average) * 100 
                                        : 0;
                                    $isOutlier = abs($diff) > (2 * $validation->standard_deviation);
                                @endphp
                                <tr class="{{ $isOutlier ? 'table-danger' : 'table-success' }}">
                                    <td>
                                        <strong>{{ $dataPoint->dataProvider->organization_name }}</strong>
                                        <br>
                                        <small class="text-muted">Skor: {{ $dataPoint->dataProvider->trust_score }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($dataPoint->value, 4) }}</strong>
                                        {{ $validation->dataset->unit }}
                                    </td>
                                    <td>
                                        @if($dataPoint->verified_value)
                                            {{ number_format($dataPoint->verified_value, 4) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dataPoint->is_verified)
                                            <span class="badge bg-success">Doğrulandı</span>
                                        @elseif($isOutlier)
                                            <span class="badge bg-danger">Aykırı</span>
                                        @else
                                            <span class="badge bg-warning">Bekliyor</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="{{ $diff > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 4) }}
                                            <br>
                                            <small>(%{{ number_format($diffPercent, 2) }})</small>
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Outliers List -->
            @php
                $outliers = json_decode($validation->outliers, true) ?? [];
            @endphp
            
            @if(count($outliers) > 0)
            <div class="card card-danger mt-3">
                <div class="card-header">
                    <h3 class="card-title">Aykırı Değerler</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Uyarı</h5>
                        <p class="mb-0">
                            Aşağıdaki sağlayıcıların verileri doğrulama aralığı dışında kalmıştır.
                            Bu veriler otomatik olarak reddedilmiştir.
                        </p>
                    </div>
                    
                    <ul class="list-group">
                        @foreach($outliers as $outlier)
                        <li class="list-group-item list-group-item-danger">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $outlier['provider'] ?? 'Bilinmeyen Sağlayıcı' }}</strong>
                                    <br>
                                    <small>Değer: {{ number_format($outlier['value'] ?? 0, 4) }} {{ $validation->dataset->unit }}</small>
                                </div>
                                <span class="badge bg-danger">
                                    Ortalamadan: %{{ 
                                        $validation->calculated_average > 0 
                                            ? number_format(abs(($outlier['value'] - $validation->calculated_average) / $validation->calculated_average) * 100, 2)
                                            : '0.00' 
                                    }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

    📁 **auth/**
      📄 **resources\views\auth\confirm-password.blade.php**
      ```php
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

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\auth\forgot-password.blade.php**
      ```php
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Şifremi Unuttum - Open Statistics Economy</title>
    
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
            <p class="login-box-msg">Şifrenizi mi unuttunuz? Buradan sıfırlayabilirsiniz.</p>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <div class="input-group mb-3">
                    <input type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus>
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

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">
                            Şifre Sıfırlama Linki Gönder
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="row">
                <div class="col-12">
                    <p class="mb-1 text-center">
                        <a href="{{ route('login') }}">Giriş sayfasına dön</a>
                    </p>
                    <p class="mb-0 text-center">
                        <a href="{{ route('register') }}" class="text-center">Yeni hesap oluştur</a>
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

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\auth\login.blade.php**
      ```php
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

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\auth\register.blade.php**
      ```php
@extends('layouts.app')

@section('title', 'Kayıt Ol')
@section('page_title', 'Kayıt Ol')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Kayıt Ol') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('İsim') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Adresi') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Şifre Tekrar') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Kayıt Ol') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <!-- OAuth Register Buttons -->
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <p class="mb-3">Veya sosyal medya hesaplarınızla kayıt olun:</p>
                            
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('auth.google') }}" class="btn btn-danger">
                                    <i class="fab fa-google"></i> Google ile Kayıt
                                </a>
                                <a href="{{ route('auth.github') }}" class="btn btn-dark">
                                    <i class="fab fa-github"></i> GitHub ile Kayıt
                                </a>
                                <a href="{{ route('auth.facebook') }}" class="btn btn-primary">
                                    <i class="fab fa-facebook"></i> Facebook ile Kayıt
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <p>Zaten hesabınız var mı? 
                                <a href="{{ route('login') }}">Giriş Yapın</a>
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

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\auth\reset-password.blade.php**
      ```php
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Şifre Sıfırla - Open Statistics Economy</title>
    
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
            <p class="login-box-msg">Yeni şifrenizi belirleyin</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                
                <div class="input-group mb-3">
                    <input type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           placeholder="Email" 
                           value="{{ $request->email }}" 
                           required 
                           readonly>
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
                           placeholder="Yeni Şifre" 
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

                <div class="input-group mb-3">
                    <input type="password" 
                           name="password_confirmation" 
                           class="form-control" 
                           placeholder="Yeni Şifre Tekrar" 
                           required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">
                            Şifremi Sıfırla
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="row">
                <div class="col-12">
                    <p class="mb-0 text-center">
                        <a href="{{ route('login') }}">Giriş sayfasına dön</a>
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

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\auth\verify-email.blade.php**
      ```php
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

      ```

--------------------------------------------------------------------------------

    📁 **components/**
      📄 **resources\views\components\auth-session-status.blade.php**
      ```php
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\components\auth-validation-errors.blade.php**
      ```php
@props(['errors'])

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'alert alert-danger']) }}>
        <div class="font-medium text-red-600">
            {{ __('Whoops! Something went wrong.') }}
        </div>

        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\components\input-error.blade.php**
      ```php
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\components\input-label.blade.php**
      ```php
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\components\primary-button.blade.php**
      ```php
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary']) }}>
    {{ $slot }}
</button>

      ```

--------------------------------------------------------------------------------

    📁 **layouts/**
      📄 **resources\views\layouts\app.blade.php**
      ```php
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Open Statistics Economy')</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('home') }}" class="nav-link">Ana Sayfa</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- User Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-user"></i>
                    @auth
                        <span>{{ Auth::user()->name }}</span>
                    @endauth
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">
                        @auth
                            {{ Auth::user()->role == 'admin' ? 'Yönetici' : 
                               (Auth::user()->role == 'statistician' ? 'İstatistikçi' : 'Veri Sağlayıcı') }}
                        @endauth
                    </span>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Profil
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="brand-link">
            <img src="https://adminlte.io/themes/v3/dist/img/AdminLTELogo.png" alt="OSE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">OSE Platform</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    @auth
                        <img src="{{ Auth::user()->avatar ?? 'https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg' }}" class="img-circle elevation-2" alt="User Image">
                    @endauth
                </div>
                <div class="info">
                    @auth
                        <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                        <small class="text-muted">
                            {{ Auth::user()->role == 'admin' ? 'Yönetici' : 
                               (Auth::user()->role == 'statistician' ? 'İstatistikçi' : 'Veri Sağlayıcı') }}
                        </small>
                    @endauth
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    @auth
                        @if(Auth::user()->role == 'admin')
                            <!-- Admin Menu -->
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Kullanıcılar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.datasets.index') }}" class="nav-link {{ request()->routeIs('admin.datasets.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-database"></i>
                                    <p>Veri Setleri</p>
                                </a>
                            </li>

                        @elseif(Auth::user()->role == 'statistician')
                            <!-- Statistician Menu -->
                            <li class="nav-item">
                                <a href="{{ route('statistician.dashboard') }}" class="nav-link {{ request()->routeIs('statistician.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('statistician.datasets.index') }}" class="nav-link {{ request()->routeIs('statistician.datasets.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-database"></i>
                                    <p>Veri Setlerim</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('statistician.rules.index') }}" class="nav-link {{ request()->routeIs('statistician.rules.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-calculator"></i>
                                    <p>Hesaplama Kuralları</p>
                                </a>
                            </li>

                        @elseif(Auth::user()->role == 'provider')
                            <!-- Provider Menu -->
                            <li class="nav-item">
                                <a href="{{ route('provider.dashboard') }}" class="nav-link {{ request()->routeIs('provider.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('provider.data-entry.index') }}" class="nav-link {{ request()->routeIs('provider.data-entry.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-edit"></i>
                                    <p>Veri Girişi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('provider.profile') }}" class="nav-link {{ request()->routeIs('provider.profile') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>Profil</p>
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="icon fas fa-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="icon fas fa-ban"></i> {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="icon fas fa-exclamation-triangle"></i> {{ session('warning') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Open Statistics Economy &copy; {{ date('Y') }}</strong>
        Tüm hakları saklıdır.
        <div class="float-right d-none d-sm-inline-block">
            <b>Versiyon</b> 1.0.0
        </div>
    </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@stack('scripts')
</body>
</html>

      ```

--------------------------------------------------------------------------------

      📁 **menu/**
        📄 **resources\views\layouts\menu\admin.blade.php**
        ```php
<li class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.users.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>
            Kullanıcı Yönetimi
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Kullanıcılar</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.users.create') }}" class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Yeni Kullanıcı</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item {{ request()->routeIs('admin.datasets.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('admin.datasets.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-database"></i>
        <p>
            Veri Setleri
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.datasets.index') }}" class="nav-link {{ request()->routeIs('admin.datasets.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Veri Setleri</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.datasets.create') }}" class="nav-link {{ request()->routeIs('admin.datasets.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Yeni Veri Seti</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('admin.validations.index') }}" class="nav-link {{ request()->routeIs('admin.validations.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-check-circle"></i>
        <p>Doğrulama Geçmişi</p>
    </a>
</li>

<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cog"></i>
        <p>Sistem Ayarları</p>
    </a>
</li>

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\layouts\menu\provider.blade.php**
        ```php
<li class="nav-item">
    <a href="{{ route('provider.dashboard') }}" class="nav-link {{ request()->routeIs('provider.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('provider.data-entry.index') }}" class="nav-link {{ request()->routeIs('provider.data-entry.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-edit"></i>
        <p>Veri Girişi</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('provider.profile') }}" class="nav-link {{ request()->routeIs('provider.profile*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>
        <p>Profilim</p>
    </a>
</li>

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\layouts\menu\statistician.blade.php**
        ```php
<li class="nav-item">
    <a href="{{ route('statistician.dashboard') }}" class="nav-link {{ request()->routeIs('statistician.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('statistician.datasets.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('statistician.datasets.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-database"></i>
        <p>
            Veri Setlerim
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('statistician.datasets.index') }}" class="nav-link {{ request()->routeIs('statistician.datasets.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Veri Setleri</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('statistician.datasets.create') }}" class="nav-link {{ request()->routeIs('statistician.datasets.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Yeni Veri Seti</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item {{ request()->routeIs('statistician.rules.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('statistician.rules.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-calculator"></i>
        <p>
            Hesaplama Kuralları
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('statistician.rules.index') }}" class="nav-link {{ request()->routeIs('statistician.rules.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Kurallar</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('statistician.rules.create') }}" class="nav-link {{ request()->routeIs('statistician.rules.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Yeni Kural</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('statistician.calculations.index') }}" class="nav-link {{ request()->routeIs('statistician.calculations.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-chart-line"></i>
        <p>Hesaplamalar</p>
    </a>
</li>

        ```

--------------------------------------------------------------------------------

    📁 **provider/**
      📄 **resources\views\provider\dashboard.blade.php**
      ```php
@extends('layouts.app')

@section('title', 'Veri Sağlayıcı Dashboard')
@section('page_title', 'Veri Sağlayıcı Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$dataProvider)
        <div class="alert alert-warning">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Profil Tamamlanmamış</h5>
            Veri girişi yapabilmek için önce profilinizi tamamlamanız gerekiyor.
            <a href="{{ route('provider.profile') }}" class="btn btn-warning btn-sm ml-3">Profili Tamamla</a>
        </div>
    @endif
    
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $verifiedDataPoints }}</h3>
                    <p>Doğrulanmış Veri</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('provider.data-entry.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingDataPoints }}</h3>
                    <p>Bekleyen Doğrulama</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('provider.data-entry.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $availableDatasets->count() }}</h3>
                    <p>Veri Girebileceğim Setler</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
                <a href="{{ route('provider.data-entry.create') }}" class="small-box-footer">
                    Veri Gir <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>
                        @if($dataProvider && $dataProvider->is_verified)
                            <i class="fas fa-check"></i>
                        @else
                            <i class="fas fa-times"></i>
                        @endif
                    </h3>
                    <p>Doğrulama Durumu</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('provider.profile') }}" class="small-box-footer">
                    Profilim <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    @if($dataProvider)
    <div class="row">
        <!-- Available Datasets -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Veri Girebileceğim Veri Setleri</h3>
                </div>
                <div class="card-body">
                    @foreach($availableDatasets as $dataset)
                    <div class="callout callout-info">
                        <h5>{{ $dataset->name }}</h5>
                        <p>{{ Str::limit($dataset->description, 100) }}</p>
                        <div class="row">
                            <div class="col-6">
                                <small>Birim: {{ $dataset->unit }}</small>
                            </div>
                            <div class="col-6 text-right">
                                @if($dataset->dataPoints->count() > 0)
                                    <small>Son giriş: {{ $dataset->dataPoints->first()->date->format('d.m.Y') }}</small>
                                @else
                                    <small>Henüz veri girilmedi</small>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('provider.data-entry.create') }}?dataset={{ $dataset->id }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Veri Ekle
                            </a>
                            <a href="{{ route('provider.data-entry.index') }}?dataset={{ $dataset->id }}" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-list"></i> Verilerim
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Recent Data Points -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Eklediğim Veriler</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Tarih</th>
                                <th>Değer</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->dataset->name }}</td>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>{{ number_format($dataPoint->value, 2) }} {{ $dataPoint->dataset->unit }}</td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('provider.data-entry.edit', $dataPoint) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('provider.data-entry.index') }}" class="btn btn-primary btn-sm">
                        Tüm Verilerim
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Provider Info -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Veri Sağlayıcı Bilgilerim</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Kurum Adı:</strong> {{ $dataProvider->organization_name }}</p>
                            @if($dataProvider->website)
                                <p><strong>Website:</strong> 
                                    <a href="{{ $dataProvider->website }}" target="_blank">{{ $dataProvider->website }}</a>
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Doğrulama Durumu:</strong> 
                                @if($dataProvider->is_verified)
                                    <span class="badge bg-success">Doğrulanmış</span>
                                @else
                                    <span class="badge bg-warning">Doğrulama Bekliyor</span>
                                @endif
                            </p>
                            <p><strong>Güven Skoru:</strong> 
                                <span class="badge bg-{{ $dataProvider->trust_score >= 80 ? 'success' : ($dataProvider->trust_score >= 60 ? 'warning' : 'danger') }}">
                                    {{ number_format($dataProvider->trust_score, 1) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($dataProvider->description)
                        <hr>
                        <p><strong>Açıklama:</strong></p>
                        <p>{{ $dataProvider->description }}</p>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('provider.profile') }}" class="btn btn-info">
                        <i class="fas fa-edit"></i> Profili Düzenle
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

      ```

--------------------------------------------------------------------------------

      📄 **resources\views\provider\profile.blade.php**
      ```php
@extends('layouts.app')

@section('title', 'Profilim')
@section('page_title', 'Profilim')

@section('breadcrumb')
    <li class="breadcrumb-item active">Profilim</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Sağlayıcı Profil Bilgileri</h3>
                </div>
                <form action="{{ route('provider.profile.update') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Önemli Bilgi</h5>
                            <p>
                                Profil bilgileriniz doğrulandıktan sonra sistemdeki güvenilirlik puanınız 
                                artacak ve verileriniz daha hızlı doğrulanacaktır.
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="organization_name">Kurum/Kuruluş Adı *</label>
                            <input type="text" class="form-control @error('organization_name') is-invalid @enderror" 
                                   id="organization_name" name="organization_name" 
                                   value="{{ old('organization_name', $dataProvider->organization_name ?? '') }}" 
                                   required>
                            @error('organization_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Resmi kurum adınızı veya şahıs olarak çalışıyorsanız adınızı yazın.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="website">Web Sitesi</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" 
                                   value="{{ old('website', $dataProvider->website ?? '') }}"
                                   placeholder="https://ornek.com">
                            @error('website')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Web siteniz varsa ekleyin. Bu, doğrulanabilirlik için önemlidir.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $dataProvider->description ?? '') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Kurumunuz hakkında kısa bir açıklama yazın. Bu, kullanıcıların sizi 
                                tanımasına yardımcı olacaktır.
                            </small>
                        </div>

                        @if($dataProvider)
                        <div class="form-group">
                            <label>Mevcut Durum</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-star"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Güven Skoru</span>
                                            <span class="info-box-number">{{ $dataProvider->trust_score ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon 
                                            @if($dataProvider->is_verified) bg-success @else bg-warning @endif">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Doğrulama</span>
                                            <span class="info-box-number">
                                                @if($dataProvider->is_verified)
                                                    Doğrulanmış
                                                @else
                                                    Bekliyor
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Profili Kaydet
                        </button>
                        <a href="{{ route('provider.dashboard') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

      ```

--------------------------------------------------------------------------------

      📁 **data-entry/**
        📄 **resources\views\provider\data-entry\create.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Yeni Veri Ekle')
@section('page_title', 'Yeni Veri Ekle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('provider.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('provider.data-entry.index') }}">Veri Girişleri</a></li>
    <li class="breadcrumb-item active">Yeni Veri</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$dataProvider)
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Erişim Engellendi</h5>
            Veri girişi yapabilmek için önce veri sağlayıcı profilinizi tamamlamanız gerekiyor.
            <a href="{{ route('provider.profile') }}" class="btn btn-danger btn-sm ml-3">Profili Tamamla</a>
        </div>
        @endsection
        @php return; @endphp
    @endif
    
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Bilgileri</h3>
                </div>
                <form action="{{ route('provider.data-entry.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="dataset_id">Veri Seti *</label>
                            <select class="form-control @error('dataset_id') is-invalid @enderror" 
                                    id="dataset_id" name="dataset_id" required>
                                <option value="">Seçiniz</option>
                                @foreach($datasets as $dataset)
                                <option value="{{ $dataset->id }}" 
                                        {{ old('dataset_id', request('dataset')) == $dataset->id ? 'selected' : '' }}>
                                    {{ $dataset->name }} ({{ $dataset->unit }})
                                </option>
                                @endforeach
                            </select>
                            @error('dataset_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="date">Tarih *</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                   id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}" required>
                            @error('date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Gelecek tarihli veri giremezsiniz. Geçmiş tarihler serbest.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="value">Değer *</label>
                            <div class="input-group">
                                <input type="number" step="0.0001" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" value="{{ old('value') }}" required 
                                       min="0" max="999999999.9999">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="unit_display">-</span>
                                </div>
                            </div>
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="source_url">Kaynak URL (Opsiyonel)</label>
                            <input type="url" class="form-control @error('source_url') is-invalid @enderror" 
                                   id="source_url" name="source_url" value="{{ old('source_url') }}" 
                                   placeholder="https://example.com/veri-kaynagi">
                            @error('source_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Veriyi doğrulamak için kaynak linki ekleyebilirsiniz.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notlar (Opsiyonel)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Veri hakkında ek bilgiler...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Veriyi Kaydet
                        </button>
                        <a href="{{ route('provider.data-entry.index') }}" class="btn btn-default">İptal</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Provider Info -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Veri Sağlayıcı Bilgileri</h3>
                </div>
                <div class="card-body">
                    <p><strong>Kurum:</strong> {{ $dataProvider->organization_name }}</p>
                    <p><strong>Doğrulama:</strong> 
                        @if($dataProvider->is_verified)
                            <span class="badge bg-success">Doğrulanmış</span>
                        @else
                            <span class="badge bg-warning">Bekliyor</span>
                        @endif
                    </p>
                    <p><strong>Güven Skoru:</strong> 
                        <span class="badge bg-{{ $dataProvider->trust_score >= 80 ? 'success' : ($dataProvider->trust_score >= 60 ? 'warning' : 'danger') }}">
                            {{ number_format($dataProvider->trust_score, 1) }}
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Duplicate Warning -->
            <div class="card card-warning mt-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Dikkat</h3>
                </div>
                <div class="card-body">
                    <p>Aynı tarih için aynı veri setine sadece <strong>bir kez</strong> veri girebilirsiniz.</p>
                    <p>Eğer aynı tarih için veri girmişseniz, lütfen eski veriyi düzenleyin.</p>
                    <div class="alert alert-light">
                        <small>
                            <strong>Veri Doğrulama Süreci:</strong><br>
                            1. Veriniz kaydedilir<br>
                            2. Aynı tarihte 2+ veri olunca otomatik doğrulama başlar<br>
                            3. Sistem ortalamayı hesaplar ve aykırı değerleri işaretler<br>
                            4. Doğrulama sonucu size bildirilir
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Recent Entries -->
            <div class="card card-primary mt-3">
                <div class="card-header">
                    <h3 class="card-title">Son Veri Girişleriniz</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @php
                            $recentEntries = $dataProvider->dataPoints()
                                ->with('dataset')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                        @endphp
                        @foreach($recentEntries as $entry)
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                {{ $entry->dataset->name }} 
                                <span class="float-right text-muted">
                                    {{ $entry->date->format('d.m') }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Dataset unit display
        function updateUnitDisplay() {
            const datasetId = $('#dataset_id').val();
            const datasets = @json($datasets->keyBy('id'));
            
            if (datasetId && datasets[datasetId]) {
                $('#unit_display').text(datasets[datasetId].unit);
            } else {
                $('#unit_display').text('-');
            }
        }
        
        $('#dataset_id').change(updateUnitDisplay);
        updateUnitDisplay(); // Initial call
        
        // Form validation
        $('form').submit(function(e) {
            const value = parseFloat($('#value').val());
            if (value < 0) {
                alert('Değer sıfırdan küçük olamaz!');
                e.preventDefault();
                return false;
            }
            
            if (value > 999999999.9999) {
                alert('Değer çok büyük!');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\provider\data-entry\edit.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Veri Düzenle')
@section('page_title', 'Veri Düzenle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('provider.data-entry.index') }}">Verilerim</a></li>
    <li class="breadcrumb-item active">Veri Düzenle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Veri Düzenle</h3>
                </div>
                <form action="{{ route('provider.data-entry.update', $dataPoint) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Dikkat</h5>
                            <ul class="mb-0">
                                <li>Veriyi güncellediğinizde doğrulama durumu sıfırlanacaktır.</li>
                                <li>Sadece değer, kaynak URL ve notları değiştirebilirsiniz.</li>
                                <li>Veri seti ve tarih değiştirilemez.</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label>Veri Seti</label>
                            <input type="text" class="form-control" value="{{ $dataPoint->dataset->name }} ({{ $dataPoint->dataset->unit }})" readonly>
                        </div>

                        <div class="form-group">
                            <label>Tarih</label>
                            <input type="text" class="form-control" value="{{ $dataPoint->date->format('d.m.Y') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="value">Değer *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       id="value" name="value" 
                                       value="{{ old('value', $dataPoint->value) }}" 
                                       step="0.0001" 
                                       min="0" 
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ $dataPoint->dataset->unit }}</span>
                                </div>
                            </div>
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="source_url">Kaynak URL (Opsiyonel)</label>
                            <input type="url" class="form-control @error('source_url') is-invalid @enderror" 
                                   id="source_url" name="source_url" 
                                   value="{{ old('source_url', $dataPoint->source_url) }}"
                                   placeholder="https://ornek.com/veri-kaynagi">
                            @error('source_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notlar (Opsiyonel)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $dataPoint->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Mevcut Durum</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon 
                                            @if($dataPoint->is_verified) bg-success @else bg-warning @endif">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Doğrulama</span>
                                            <span class="info-box-number">
                                                @if($dataPoint->is_verified)
                                                    Doğrulanmış
                                                @else
                                                    Bekliyor
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-history"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Son Güncelleme</span>
                                            <span class="info-box-number">
                                                {{ $dataPoint->updated_at->format('d.m.Y H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('provider.data-entry.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\provider\data-entry\index.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Veri Girişlerim')
@section('page_title', 'Veri Girişlerim')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('provider.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Veri Girişleri</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$dataProvider)
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Erişim Engellendi</h5>
            Veri girişi yapabilmek için önce veri sağlayıcı profilinizi tamamlamanız gerekiyor.
            <a href="{{ route('provider.profile') }}" class="btn btn-danger btn-sm ml-3">Profili Tamamla</a>
        </div>
        @endsection
        @php return; @endphp
    @endif
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veri Girişlerim</h3>
                    <div class="card-tools">
                        <a href="{{ route('provider.data-entry.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Veri Ekle
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Veri Seti</th>
                                <th>Tarih</th>
                                <th>Girilen Değer</th>
                                <th>Doğrulanmış Değer</th>
                                <th>Durum</th>
                                <th>Kaynak</th>
                                <th>Notlar</th>
                                <th>İşlem Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myDataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->id }}</td>
                                <td>{{ $dataPoint->dataset->name }}</td>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>{{ number_format($dataPoint->value, 4) }} {{ $dataPoint->dataset->unit }}</td>
                                <td>
                                    @if($dataPoint->verified_value)
                                        {{ number_format($dataPoint->verified_value, 4) }} {{ $dataPoint->dataset->unit }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->source_url)
                                        <a href="{{ $dataPoint->source_url }}" target="_blank" class="btn btn-xs btn-info">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ Str::limit($dataPoint->notes, 20) }}</td>
                                <td>{{ $dataPoint->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('provider.data-entry.edit', $dataPoint) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('provider.data-entry.destroy', $dataPoint) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu veriyi silmek istediğinize emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $myDataPoints->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mt-3">
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Toplam Veri</span>
                    <span class="info-box-number">{{ $myDataPoints->total() }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Doğrulanmış</span>
                    <span class="info-box-number">
                        {{ $dataProvider->dataPoints()->where('is_verified', true)->count() }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bekleyen</span>
                    <span class="info-box-number">
                        {{ $dataProvider->dataPoints()->where('is_verified', false)->count() }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Doğrulama Oranı</span>
                    <span class="info-box-number">
                        @php
                            $total = $dataProvider->dataPoints()->count();
                            $verified = $dataProvider->dataPoints()->where('is_verified', true)->count();
                            $rate = $total > 0 ? round(($verified / $total) * 100, 1) : 0;
                        @endphp
                        {{ $rate }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

    📁 **statistician/**
      📄 **resources\views\statistician\dashboard.blade.php**
      ```php
@extends('layouts.app')

@section('title', 'İstatistikçi Dashboard')
@section('page_title', 'İstatistikçi Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $myDatasets->count() }}</h3>
                    <p>Veri Setlerim</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
                <a href="{{ route('statistician.datasets.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $pendingValidations->count() }}</h3>
                    <p>Bekleyen Doğrulama</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('statistician.datasets.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ count($calculatedValues) }}</h3>
                    <p>Hesaplanan Değer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <a href="{{ route('statistician.calculations.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    @php
                        $totalDataPoints = 0;
                        foreach($myDatasets as $dataset) {
                            $totalDataPoints += $dataset->data_points_count;
                        }
                    @endphp
                    <h3>{{ $totalDataPoints }}</h3>
                    <p>Toplam Veri Noktası</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <a href="{{ route('statistician.datasets.index') }}" class="small-box-footer">
                    Detaylı Gör <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Data Points -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Eklenen Veri Noktaları</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Sağlayıcı</th>
                                <th>Tarih</th>
                                <th>Değer</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->dataset->name }}</td>
                                <td>{{ $dataPoint->dataProvider->organization_name ?? '-' }}</td>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>{{ number_format($dataPoint->value, 2) }} {{ $dataPoint->dataset->unit }}</td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Calculated Values -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hesaplanan Değerler</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Hesaplanan Değer</th>
                                <th>Birim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculatedValues as $value)
                            <tr>
                                <td>{{ $value['name'] }}</td>
                                <td>
                                    @if($value['value'] !== null)
                                        <strong>{{ number_format($value['value'], 2) }}</strong>
                                    @else
                                        <span class="text-muted">Hesaplanamadı</span>
                                    @endif
                                </td>
                                <td>{{ $value['unit'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('statistician.calculations.index') }}" class="btn btn-primary btn-sm">
                        Tüm Hesaplamalar
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- My Datasets -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Veri Setlerim</h3>
                    <div class="card-tools">
                        <a href="{{ route('statistician.datasets.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Veri Seti
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>İsim</th>
                                <th>Açıklama</th>
                                <th>Veri Noktası</th>
                                <th>Doğrulama</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myDatasets as $dataset)
                            <tr>
                                <td>{{ $dataset->name }}</td>
                                <td>{{ Str::limit($dataset->description, 50) }}</td>
                                <td>{{ $dataset->data_points_count }}</td>
                                <td>{{ $dataset->validation_logs_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $dataset->is_public ? 'success' : 'secondary' }}">
                                        {{ $dataset->is_public ? 'Açık' : 'Kapalı' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('statistician.datasets.show', $dataset) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('statistician.datasets.index') }}" class="btn btn-primary">Tüm Veri Setleri</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

      ```

--------------------------------------------------------------------------------

      📁 **calculations/**
        📄 **resources\views\statistician\calculations\index.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Hesaplama Sonuçları')
@section('page_title', 'Hesaplama Sonuçları')

@section('breadcrumb')
    <li class="breadcrumb-item active">Hesaplamalar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('statistician.calculations.run-all') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" 
                        onclick="return confirm('Tüm hesaplama kurallarını çalıştırmak istediğinizden emin misiniz?')">
                    <i class="fas fa-calculator"></i> Tümünü Hesapla
                </button>
            </form>
            
            <a href="{{ route('statistician.rules.index') }}" class="btn btn-default float-right">
                <i class="fas fa-arrow-left"></i> Kurallara Dön
            </a>
        </div>
    </div>

    @if($calculations->isEmpty())
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calculator fa-4x text-muted mb-3"></i>
                        <h4>Henüz Hesaplama Yok</h4>
                        <p class="text-muted">
                            Hesaplama kuralı tanımlanmış veri setiniz bulunmuyor.
                            Önce bir veri seti oluşturun ve hesaplama kuralı ekleyin.
                        </p>
                        <a href="{{ route('statistician.datasets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Yeni Veri Seti
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($calculations as $calculation)
            <div class="col-md-4 mb-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">{{ $calculation['dataset']->name }}</h3>
                        <div class="card-tools">
                            <span class="badge bg-info">{{ $calculation['dataset']->data_points_count }} veri</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if($calculation['result'] !== null)
                                <h2 class="display-4 text-success">
                                    {{ number_format($calculation['result'], 4) }}
                                    <small class="text-muted">{{ $calculation['dataset']->unit }}</small>
                                </h2>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Hesaplanamadı
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <strong>Hesaplama Kuralı:</strong>
                            <code class="d-block mt-1 p-2 bg-light rounded">
                                {{ $calculation['formula'] }}
                            </code>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Açıklama:</strong>
                            <p class="mb-0">{{ Str::limit($calculation['dataset']->description, 100) }}</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i>
                                    {{ $calculation['dataset']->created_at->format('d.m.Y') }}
                                </small>
                            </div>
                            <div class="col-6 text-right">
                                <small class="text-muted">
                                    @if($calculation['dataset']->is_public)
                                        <span class="badge bg-success">Açık</span>
                                    @else
                                        <span class="badge bg-warning">Kapalı</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100">
                            <a href="{{ route('statistician.calculations.show', $calculation['dataset']) }}" 
                               class="btn btn-success">
                                <i class="fas fa-chart-line"></i> Detaylar
                            </a>
                            <a href="{{ route('statistician.datasets.show', $calculation['dataset']) }}" 
                               class="btn btn-outline-success">
                                <i class="fas fa-eye"></i> Veriler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary Stats -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Hesaplama Özeti</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-calculator"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Toplam Hesaplama</span>
                                        <span class="info-box-number">{{ count($calculations) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Başarılı</span>
                                        <span class="info-box-number">
                                            {{ $calculations->where('result', '!==', null)->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Başarısız</span>
                                        <span class="info-box-number">
                                            {{ $calculations->where('result', '===', null)->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-database"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Ort. Veri</span>
                                        <span class="info-box-number">
                                            {{ round($calculations->avg(function($c) { return $c['dataset']->data_points_count; }), 1) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\statistician\calculations\show.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Hesaplama Detayları: ' . $dataset->name)
@section('page_title', 'Hesaplama Detayları: ' . $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.calculations.index') }}">Hesaplamalar</a></li>
    <li class="breadcrumb-item active">Detaylar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Calculation Result -->
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Hesaplama Sonucu</h3>
                </div>
                <div class="card-body text-center">
                    @if($result !== null)
                        <div class="display-4 text-success mb-3">
                            {{ number_format($result, 4) }}
                            <small class="text-muted">{{ $dataset->unit }}</small>
                        </div>
                        
                        <div class="alert alert-light">
                            <strong>Hesaplama Kuralı:</strong>
                            <code class="d-block mt-2 p-2 bg-white rounded">
                                {{ $dataset->calculation_rule }}
                            </code>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h4><i class="fas fa-exclamation-triangle"></i> Hesaplanamadı</h4>
                            <p class="mb-0">
                                Bu veri seti için hesaplama yapılamadı.
                                Lütfen hesaplama kuralını kontrol edin veya yeterli veri olduğundan emin olun.
                            </p>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Kuralı Düzenle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dataset Info -->
            <div class="card card-info mt-3">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>İsim:</dt>
                        <dd>{{ $dataset->name }}</dd>
                        
                        <dt>Açıklama:</dt>
                        <dd>{{ $dataset->description ?? 'Belirtilmemiş' }}</dd>
                        
                        <dt>Birim:</dt>
                        <dd>{{ $dataset->unit }}</dd>
                        
                        <dt>Veri Noktası:</dt>
                        <dd>{{ $dataset->dataPoints()->count() }}</dd>
                        
                        <dt>Doğrulanmış Veri:</dt>
                        <dd>{{ $dataset->dataPoints()->where('is_verified', true)->count() }}</dd>
                        
                        <dt>Durum:</dt>
                        <dd>
                            @if($dataset->is_public)
                                <span class="badge bg-success">Açık</span>
                            @else
                                <span class="badge bg-warning">Kapalı</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Calculation History -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hesaplama Geçmişi (Son 30 Gün)</h3>
                </div>
                <div class="card-body">
                    @if(empty($history))
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Geçmiş Veri Yok</h5>
                            <p class="mb-0">
                                Bu veri seti için son 30 günlük hesaplama geçmişi bulunmuyor.
                                Bu, yeni bir veri seti olabilir veya yeterli veri bulunmuyor olabilir.
                            </p>
                        </div>
                    @else
                        <div class="chart-container">
                            <canvas id="calculationHistoryChart"></canvas>
                        </div>
                        
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Değer</th>
                                        <th>Veri Sayısı</th>
                                        <th>Değişim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $previousValue = null;
                                    @endphp
                                    @foreach($history as $record)
                                    <tr>
                                        <td>{{ $record['date']->format('d.m.Y') }}</td>
                                        <td>
                                            <strong>{{ number_format($record['value'], 4) }}</strong>
                                            {{ $dataset->unit }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $record['count'] }}</span>
                                        </td>
                                        <td>
                                            @if($previousValue !== null)
                                                @php
                                                    $change = $record['value'] - $previousValue;
                                                    $changePercent = $previousValue != 0 
                                                        ? ($change / $previousValue) * 100 
                                                        : 0;
                                                @endphp
                                                <span class="{{ $change > 0 ? 'text-success' : ($change < 0 ? 'text-danger' : 'text-muted') }}">
                                                    {{ $change > 0 ? '+' : '' }}{{ number_format($change, 4) }}
                                                    <br>
                                                    <small>(%{{ number_format($changePercent, 2) }})</small>
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $previousValue = $record['value'];
                                    @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Data Points Used -->
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Hesaplamada Kullanılan Veriler</h3>
                </div>
                <div class="card-body">
                    @php
                        $dataPoints = $dataset->dataPoints()
                            ->where('is_verified', true)
                            ->orderBy('date', 'desc')
                            ->limit(50)
                            ->get();
                    @endphp
                    
                    @if($dataPoints->isEmpty())
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Doğrulanmış Veri Yok</h5>
                            <p class="mb-0">
                                Bu veri setinde henüz doğrulanmış veri bulunmuyor.
                                Hesaplama yapabilmek için önce verilerin doğrulanması gerekiyor.
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarih</th>
                                        <th>Sağlayıcı</th>
                                        <th>Değer</th>
                                        <th>Doğrulanma Tarihi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataPoints as $dataPoint)
                                    <tr>
                                        <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                        <td>{{ $dataPoint->dataProvider->organization_name }}</td>
                                        <td>
                                            <strong>{{ number_format($dataPoint->verified_value ?? $dataPoint->value, 4) }}</strong>
                                            {{ $dataset->unit }}
                                        </td>
                                        <td>{{ $dataPoint->updated_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-2">
                            <a href="{{ route('statistician.datasets.show', $dataset) }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> Tüm Verileri Gör
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(!empty($history))
            var ctx = document.getElementById('calculationHistoryChart').getContext('2d');
            
            var labels = @json(array_map(function($record) {
                return $record['date']->format('d.m.Y');
            }, $history));
            
            var values = @json(array_map(function($record) {
                return $record['value'];
            }, $history));
            
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '{{ $dataset->name }} ({{ $dataset->unit }})',
                        data: values,
                        borderColor: 'rgb(40, 167, 69)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: 'rgb(40, 167, 69)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toFixed(4);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tarih'
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: '{{ $dataset->unit }}'
                            },
                            beginAtZero: false
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        @endif
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

      📁 **datasets/**
        📄 **resources\views\statistician\datasets\create.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Yeni Veri Seti')
@section('page_title', 'Yeni Veri Seti Oluştur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('statistician.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item active">Yeni Veri Seti</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                </div>
                <form action="{{ route('statistician.datasets.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Veri Seti Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="unit">Birim *</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                   id="unit" name="unit" value="{{ old('unit') }}" required 
                                   placeholder="Örn: TL, USD, Adet, %">
                            @error('unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="calculation_rule">Hesaplama Kuralı (DSL)</label>
                            <textarea class="form-control @error('calculation_rule') is-invalid @enderror" 
                                      id="calculation_rule" name="calculation_rule" rows="4" 
                                      placeholder="Örn: ortalama(deger), topla(deger) / sayi">{{ old('calculation_rule') }}</textarea>
                            @error('calculation_rule')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Kullanılabilir fonksiyonlar: ortalama(deger), topla(deger), max(deger), min(deger), sayi
                            </small>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" 
                                   id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : 'checked' }}>
                            <label class="form-check-label" for="is_public">Herkes görebilsin</label>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Veri Seti Oluştur</button>
                        <a href="{{ route('statistician.datasets.index') }}" class="btn btn-default">İptal</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">DSL Örnekleri</h3>
                </div>
                <div class="card-body">
                    <h5>Basit Fonksiyonlar:</h5>
                    <ul>
                        <li><code>ortalama(deger)</code> - Ortalama hesaplama</li>
                        <li><code>topla(deger)</code> - Toplam hesaplama</li>
                        <li><code>max(deger)</code> - Maksimum değer</li>
                        <li><code>min(deger)</code> - Minimum değer</li>
                        <li><code>sayi</code> - Veri noktası sayısı</li>
                    </ul>
                    
                    <h5>Kompleks İfadeler:</h5>
                    <ul>
                        <li><code>topla(deger) / sayi</code> - Ortalama (alternatif)</li>
                        <li><code>(max(deger) - min(deger)) / 2</code> - Ortalama fark</li>
                        <li><code>(ortalama(deger) * 1.18) - 5</code> - Formül uygulama</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Not:</strong> Hesaplama sadece doğrulanmış veri noktaları üzerinden yapılır.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\statistician\datasets\edit.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Veri Seti Düzenle')
@section('page_title', 'Veri Seti Düzenle: ' . $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.datasets.index') }}">Veri Setlerim</a></li>
    <li class="breadcrumb-item"><a href="{{ route('statistician.datasets.show', $dataset) }}">{{ $dataset->name }}</a></li>
    <li class="breadcrumb-item active">Düzenle</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Düzenle</h3>
                </div>
                <form action="{{ route('statistician.datasets.update', $dataset) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Veri Seti Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $dataset->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $dataset->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unit">Birim *</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                   id="unit" name="unit" value="{{ old('unit', $dataset->unit) }}" required>
                            @error('unit')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="calculation_rule">Hesaplama Kuralı (DSL)</label>
                            <textarea class="form-control @error('calculation_rule') is-invalid @enderror" 
                                      id="calculation_rule" name="calculation_rule" rows="4">{{ old('calculation_rule', $dataset->calculation_rule) }}</textarea>
                            @error('calculation_rule')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Kullanılabilir fonksiyonlar: ortalama(), topla(), max(), min(), sayi
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="is_public" name="is_public" value="1" 
                                       {{ old('is_public', $dataset->is_public) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_public">
                                    Veri seti herkese açık olsun
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Güncelle
                        </button>
                        <a href="{{ route('statistician.datasets.show', $dataset) }}" class="btn btn-default">
                            <i class="fas fa-times"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\statistician\datasets\index.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Veri Setlerim')
@section('page_title', 'Veri Setlerim')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Veri Setleri</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Veri Setleri</h3>
                    <div class="card-tools">
                        <a href="{{ route('statistician.datasets.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Veri Seti
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İsim</th>
                                <th>Açıklama</th>
                                <th>Birim</th>
                                <th>Veri Noktası</th>
                                <th>Doğrulama</th>
                                <th>Durum</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datasets as $dataset)
                            <tr>
                                <td>{{ $dataset->id }}</td>
                                <td>{{ $dataset->name }}</td>
                                <td>{{ Str::limit($dataset->description, 50) }}</td>
                                <td>{{ $dataset->unit }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $dataset->data_points_count }}</span>
                                </td>
                                <td>
                                    @if($dataset->calculation_rule)
                                        <span class="badge bg-success">Kural Var</span>
                                    @else
                                        <span class="badge bg-secondary">Kural Yok</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $dataset->is_public ? 'success' : 'secondary' }}">
                                        {{ $dataset->is_public ? 'Açık' : 'Kapalı' }}
                                    </span>
                                </td>
                                <td>{{ $dataset->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('statistician.datasets.show', $dataset) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('statistician.datasets.destroy', $dataset) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bu veri setini silmek istediğinize emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $datasets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\statistician\datasets\show.blade.php**
        ```php
@extends('layouts.app')

@section('title', $dataset->name . ' Detay')
@section('page_title', $dataset->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('statistician.datasets.index') }}">Veri Setleri</a></li>
    <li class="breadcrumb-item active">{{ $dataset->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Dataset Info Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Veri Seti Bilgileri</h3>
                    <div class="card-tools">
                        <span class="badge bg-{{ $dataset->is_public ? 'success' : 'secondary' }}">
                            {{ $dataset->is_public ? 'Açık' : 'Kapalı' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Oluşturan:</strong> {{ $dataset->creator->name }}</p>
                            <p><strong>Birim:</strong> {{ $dataset->unit }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Oluşturulma:</strong> {{ $dataset->created_at->format('d.m.Y H:i') }}</p>
                            <p><strong>Son Güncelleme:</strong> {{ $dataset->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($dataset->description)
                        <hr>
                        <p><strong>Açıklama:</strong></p>
                        <p>{{ $dataset->description }}</p>
                    @endif
                    
                    @if($dataset->calculation_rule)
                        <hr>
                        <p><strong>Hesaplama Kuralı:</strong></p>
                        <div class="alert alert-info">
                            <code>{{ $dataset->calculation_rule }}</code>
                        </div>
                        @if($calculatedValue !== null)
                            <p><strong>Hesaplanan Değer:</strong> 
                                <span class="badge bg-success" style="font-size: 1.2em;">
                                    {{ number_format($calculatedValue, 2) }} {{ $dataset->unit }}
                                </span>
                            </p>
                        @endif
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                    
                    <!-- Manual Verification Form -->
                    <form action="{{ route('statistician.datasets.verify', $dataset) }}" method="POST" class="d-inline">
                        @csrf
                        <div class="input-group input-group-sm" style="width: 300px; display: inline-flex;">
                            <input type="date" class="form-control" name="date" 
                                   value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-check"></i> Doğrula
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Chart Card -->
            <div class="card card-success mt-3">
                <div class="card-header">
                    <h3 class="card-title">Veri Grafiği</h3>
                </div>
                <div class="card-body">
                    <canvas id="datasetChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Stats Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">İstatistikler</h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Toplam Veri Noktası</span>
                            <span class="info-box-number">{{ $dataPoints->total() }}</span>
                        </div>
                    </div>
                    
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Doğrulanmış Veri</span>
                            <span class="info-box-number">
                                {{ $dataset->dataPoints()->where('is_verified', true)->count() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Bekleyen Doğrulama</span>
                            <span class="info-box-number">
                                {{ $dataset->dataPoints()->where('is_verified', false)->count() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Veri Sağlayıcı Sayısı</span>
                            <span class="info-box-number">
                                {{ $dataset->dataPoints()->distinct('data_provider_id')->count('data_provider_id') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Validations -->
            <div class="card card-warning mt-3">
                <div class="card-header">
                    <h3 class="card-title">Son Doğrulamalar</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @foreach($validationLogs as $log)
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                {{ $log->date->format('d.m.Y') }}
                                <span class="float-right badge bg-{{ $log->status === 'verified' ? 'success' : ($log->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ $log->valid_points }}/{{ $log->total_points }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Points Table -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Veri Noktaları</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Ara...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Veri Sağlayıcı</th>
                                <th>Değer</th>
                                <th>Doğrulanmış Değer</th>
                                <th>Kaynak</th>
                                <th>Durum</th>
                                <th>Notlar</th>
                                <th>İşlem Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataPoints as $dataPoint)
                            <tr>
                                <td>{{ $dataPoint->date->format('d.m.Y') }}</td>
                                <td>{{ $dataPoint->dataProvider->organization_name ?? '-' }}</td>
                                <td>{{ number_format($dataPoint->value, 4) }}</td>
                                <td>
                                    @if($dataPoint->verified_value)
                                        {{ number_format($dataPoint->verified_value, 4) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->source_url)
                                        <a href="{{ $dataPoint->source_url }}" target="_blank" class="btn btn-xs btn-info">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if($dataPoint->is_verified)
                                        <span class="badge bg-success">Doğrulanmış</span>
                                    @else
                                        <span class="badge bg-warning">Bekliyor</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($dataPoint->notes, 30) }}</td>
                                <td>{{ $dataPoint->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $dataPoints->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Chart Data
        const chartData = @json($chartData);
        
        // Create Chart
        const ctx = document.getElementById('datasetChart').getContext('2d');
        const datasetChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: '{{ $dataset->name }} ({{ $dataset->unit }})',
                    data: chartData.values,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' {{ $dataset->unit }}';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: '{{ $dataset->unit }}'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tarih'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

      📁 **rules/**
        📄 **resources\views\statistician\rules\calculations.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Hesaplama Sonuçları')
@section('page_title', 'Tüm Hesaplama Sonuçları')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.rules.index') }}">Hesaplama Kuralları</a></li>
    <li class="breadcrumb-item active">Hesaplama Sonuçları</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hesaplama Sonuçları</h3>
                    <div class="card-tools">
                        <a href="{{ route('statistician.rules.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($results))
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Bilgi</h5>
                            <p>Henüz hesaplama kuralı tanımlanmış veri setiniz bulunmuyor.</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($results as $id => $result)
                            <div class="col-md-4 mb-3">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ $result['name'] }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <h2 class="display-4">
                                                {{ number_format($result['value'], 4) }}
                                                <small class="text-muted">{{ $result['unit'] }}</small>
                                            </h2>
                                            <p class="text-muted">
                                                <small>Hesaplama Kuralı:</small><br>
                                                <code>{{ $result['rule'] }}</code>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('statistician.datasets.show', $id) }}" 
                                           class="btn btn-success btn-block">
                                            <i class="fas fa-chart-line"></i> Detaylı Görüntüle
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\statistician\rules\create.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Yeni Hesaplama Kuralı')
@section('page_title', 'Yeni Hesaplama Kuralı Oluştur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.rules.index') }}">Hesaplama Kuralları</a></li>
    <li class="breadcrumb-item active">Yeni Oluştur</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Yeni Hesaplama Kuralı</h3>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Bilgi</h5>
                        <p>
                            Hesaplama kurallarını oluşturmak için önce bir veri seti oluşturmalı, 
                            ardından veri setinin düzenleme sayfasından hesaplama kuralını eklemelisiniz.
                        </p>
                        <a href="{{ route('statistician.datasets.create') }}" class="btn btn-info">
                            <i class="fas fa-plus"></i> Yeni Veri Seti Oluştur
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Mevcut Veri Setleriniz</h4>
                                </div>
                                <div class="card-body">
                                    @if($datasets->isEmpty())
                                        <div class="alert alert-warning">
                                            Henüz veri setiniz bulunmuyor. Önce bir veri seti oluşturun.
                                        </div>
                                    @else
                                        <div class="list-group">
                                            @foreach($datasets as $dataset)
                                                <a href="{{ route('statistician.datasets.edit', $dataset) }}" 
                                                   class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">{{ $dataset->name }}</h5>
                                                        <small>{{ $dataset->unit }}</small>
                                                    </div>
                                                    <p class="mb-1">{{ Str::limit($dataset->description, 100) }}</p>
                                                    <small>
                                                        @if($dataset->calculation_rule)
                                                            <span class="text-success">Kural tanımlı</span>
                                                        @else
                                                            <span class="text-warning">Kural tanımlanmamış</span>
                                                        @endif
                                                    </small>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Example Rules -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h4 class="card-title">Örnek Hesaplama Kuralları</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Açıklama</th>
                                                <th>DSL Kuralı</th>
                                                <th>Matematiksel İfade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($exampleRules as $description => $rule)
                                            <tr>
                                                <td>{{ $description }}</td>
                                                <td><code>{{ $rule }}</code></td>
                                                <td class="text-muted">
                                                    @if($description == 'Ortalama Hesaplama')
                                                        (Σx) / n
                                                    @elseif($description == 'Toplam ve Bölme')
                                                        Σx / n
                                                    @elseif($description == 'Maksimum ve Minimum Fark')
                                                        (max(x) - min(x)) / 2
                                                    @elseif($description == 'Standart Sapma Hesaplama')
                                                        √(Σ(x - μ)² / n)
                                                    @elseif($description == 'Değişim Oranı')
                                                        ((xₙ - x₁) / x₁) × 100
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DSL Test Area -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h4 class="card-title">Kural Test Alanı</h4>
                                </div>
                                <div class="card-body">
                                    <form id="testRuleForm">
                                        @csrf
                                        <div class="form-group">
                                            <label for="test_dataset_id">Test Edilecek Veri Seti</label>
                                            <select class="form-control" id="test_dataset_id" required>
                                                <option value="">Seçiniz</option>
                                                @foreach($datasets as $dataset)
                                                    <option value="{{ $dataset->id }}">
                                                        {{ $dataset->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="test_rule">Test Kuralı</label>
                                            <textarea class="form-control" id="test_rule" rows="3" 
                                                      placeholder="ortalama(deger)"></textarea>
                                        </div>
                                        
                                        <button type="button" class="btn btn-warning" id="testRuleBtn">
                                            <i class="fas fa-play"></i> Kuralı Test Et
                                        </button>
                                        
                                        <div id="testResult" class="mt-3" style="display: none;">
                                            <div class="alert alert-success">
                                                <h5><i class="fas fa-check"></i> Test Sonucu</h5>
                                                <p id="resultText"></p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#testRuleBtn').click(function() {
            var datasetId = $('#test_dataset_id').val();
            var rule = $('#test_rule').val();
            
            if (!datasetId || !rule) {
                alert('Lütfen veri seti ve kural girin.');
                return;
            }
            
            $.ajax({
                url: '{{ route("statistician.rules.test") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dataset_id: datasetId,
                    rule: rule
                },
                success: function(response) {
                    if (response.success) {
                        $('#resultText').text('Sonuç: ' + (response.result ? response.result.toFixed(4) : 'Hesaplanamadı'));
                        $('#testResult').show();
                    }
                },
                error: function(xhr) {
                    alert('Test sırasında bir hata oluştu.');
                }
            });
        });
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

        📄 **resources\views\statistician\rules\index.blade.php**
        ```php
@extends('layouts.app')

@section('title', 'Hesaplama Kuralları')
@section('page_title', 'Hesaplama Kuralları')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('statistician.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Hesaplama Kuralları</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tüm Hesaplama Kuralları</h3>
                    <div class="card-tools">
                        <a href="{{ route('statistician.rules.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Yeni Kural
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#testRuleModal">
                            <i class="fas fa-vial"></i> Kural Test Et
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Veri Seti</th>
                                <th>Hesaplama Kuralı</th>
                                <th>Sonuç</th>
                                <th>Birim</th>
                                <th>Durum</th>
                                <th>Veri Noktası</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datasets as $dataset)
                            <tr>
                                <td>
                                    <a href="{{ route('statistician.datasets.show', $dataset) }}">
                                        {{ $dataset->name }}
                                    </a>
                                </td>
                                <td>
                                    <code>{{ $dataset->calculation_rule }}</code>
                                </td>
                                <td>
                                    @if(isset($results[$dataset->id]) && $results[$dataset->id] !== null)
                                        <strong>{{ number_format($results[$dataset->id], 4) }}</strong>
                                    @else
                                        <span class="text-muted">Hesaplanamadı</span>
                                    @endif
                                </td>
                                <td>{{ $dataset->unit }}</td>
                                <td>
                                    @if($dataset->calculation_rule)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $dataset->data_points_count }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('statistician.datasets.edit', $dataset) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Düzenle
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $datasets->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- DSL Info Card -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-code"></i> DSL (Domain-Specific Language) Rehberi</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Kullanılabilir Fonksiyonlar:</h5>
                            <ul>
                                <li><code>ortalama(deger)</code> veya <code>mean(deger)</code> - Ortalama hesaplama</li>
                                <li><code>topla(deger)</code> - Toplam hesaplama</li>
                                <li><code>max(deger)</code> - Maksimum değer</li>
                                <li><code>min(deger)</code> - Minimum değer</li>
                                <li><code>sayi</code> - Veri noktası sayısı</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Örnek Kurallar:</h5>
                            <ul>
                                <li><code>ortalama(deger)</code> - Basit ortalama</li>
                                <li><code>topla(deger) / sayi</code> - Ortalama (alternatif)</li>
                                <li><code>(max(deger) - min(deger)) / 2</code> - Ortalama fark</li>
                                <li><code>(ortalama(deger) * 1.18) - 5</code> - Formül uygulama</li>
                                <li><code>sqrt(topla((deger - ortalama(deger))^2) / sayi)</code> - Standart sapma</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Rule Modal -->
<div class="modal fade" id="testRuleModal" tabindex="-1" role="dialog" aria-labelledby="testRuleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testRuleModalLabel">Kural Test Et</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testRuleForm">
                    @csrf
                    <div class="form-group">
                        <label for="test_dataset_id">Veri Seti</label>
                        <select class="form-control" id="test_dataset_id" name="dataset_id" required>
                            <option value="">Seçiniz</option>
                            @foreach($datasets as $dataset)
                            <option value="{{ $dataset->id }}">{{ $dataset->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="test_rule">Kural İfadesi</label>
                        <textarea class="form-control" id="test_rule" name="rule" rows="3" 
                                  placeholder="ortalama(deger)" required></textarea>
                    </div>
                    <div class="form-group">
                        <div id="testResult" style="display: none;">
                            <hr>
                            <h5>Test Sonucu:</h5>
                            <div class="alert alert-success">
                                <strong id="resultValue">0</strong>
                                <span id="resultUnit"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" id="runTestBtn">
                    <i class="fas fa-play"></i> Testi Çalıştır
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Test Rule
        $('#runTestBtn').click(function() {
            const datasetId = $('#test_dataset_id').val();
            const rule = $('#test_rule').val();
            
            if (!datasetId || !rule) {
                alert('Lütfen tüm alanları doldurun!');
                return;
            }
            
            $.ajax({
                url: '{{ route("statistician.rules.test") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dataset_id: datasetId,
                    rule: rule
                },
                success: function(response) {
                    if (response.success) {
                        $('#resultValue').text(response.result !== null ? response.result.toFixed(4) : 'Hesaplanamadı');
                        
                        // Get unit from dataset
                        const datasets = @json($datasets->keyBy('id'));
                        if (datasets[datasetId]) {
                            $('#resultUnit').text(' ' + datasets[datasetId].unit);
                        }
                        
                        $('#testResult').show();
                    } else {
                        alert('Test başarısız: ' + (response.message || 'Bilinmeyen hata'));
                    }
                },
                error: function(xhr) {
                    alert('Sunucu hatası: ' + xhr.responseText);
                }
            });
        });
        
        // Auto-fill rule when dataset selected
        $('#test_dataset_id').change(function() {
            const datasetId = $(this).val();
            const datasets = @json($datasets->keyBy('id'));
            
            if (datasetId && datasets[datasetId] && datasets[datasetId].calculation_rule) {
                $('#test_rule').val(datasets[datasetId].calculation_rule);
            } else {
                $('#test_rule').val('');
            }
        });
    });
</script>
@endpush

        ```

--------------------------------------------------------------------------------

📁 **routes/**
  📄 **routes\admin.php**
  ```php
<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Admin\ValidationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('providers/{provider}/verify', [UserManagementController::class, 'verifyProvider'])
        ->name('providers.verify');
    
    // Dataset Management
    Route::resource('datasets', DatasetController::class);
    
    // Validation Management
    Route::get('validations', [ValidationController::class, 'index'])->name('validations.index');
    Route::get('validations/{validation}', [ValidationController::class, 'show'])->name('validations.show');
    Route::post('validations/{validation}/retry', [ValidationController::class, 'retry'])->name('validations.retry');
    
    // System Statistics
    Route::get('statistics', [DashboardController::class, 'statistics'])->name('statistics');
});

  ```

--------------------------------------------------------------------------------

  📄 **routes\api.php**
  ```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

  ```

--------------------------------------------------------------------------------

  📄 **routes\auth.php**
  ```php
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware(['throttle:6,1'])
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});

  ```

--------------------------------------------------------------------------------

  📄 **routes\channels.php**
  ```php
<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

  ```

--------------------------------------------------------------------------------

  📄 **routes\console.php**
  ```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

  ```

--------------------------------------------------------------------------------

  📄 **routes\provider.php**
  ```php
<?php

use App\Http\Controllers\Provider\DashboardController;
use App\Http\Controllers\Provider\DataEntryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Data Entry
    Route::resource('data-entry', DataEntryController::class)->except(['show']);
    
    // Quick Actions
    Route::get('quick-entry', [DataEntryController::class, 'quickEntry'])->name('quick-entry');
    Route::post('quick-entry', [DataEntryController::class, 'storeQuickEntry'])->name('quick-entry.store');
    
    // Data Statistics
    Route::get('statistics', [DashboardController::class, 'statistics'])->name('statistics');
    
    // Verification Status
    Route::get('verification-status', [DataEntryController::class, 'verificationStatus'])->name('verification-status');
});

  ```

--------------------------------------------------------------------------------

  📄 **routes\statistician.php**
  ```php
<?php

use App\Http\Controllers\Statistician\DashboardController;
use App\Http\Controllers\Statistician\DatasetController;
use App\Http\Controllers\Statistician\RuleController;
use App\Http\Controllers\Statistician\CalculationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:statistician'])->prefix('statistician')->name('statistician.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Dataset Management
    Route::resource('datasets', DatasetController::class);
    Route::post('datasets/{dataset}/verify', [DatasetController::class, 'verifyData'])->name('datasets.verify');
    Route::get('datasets/{dataset}/chart', [DatasetController::class, 'chartData'])->name('datasets.chart');
    
    // Rule Management
    Route::get('rules', [RuleController::class, 'index'])->name('rules.index');
    Route::get('rules/create', [RuleController::class, 'create'])->name('rules.create');
    Route::post('rules/test', [RuleController::class, 'testRule'])->name('rules.test');
    Route::get('rules/calculate', [RuleController::class, 'calculateAll'])->name('rules.calculate');
    Route::get('rules/{dataset}/edit', [RuleController::class, 'edit'])->name('rules.edit');
    
    // Calculation Management
    Route::get('calculations', [CalculationController::class, 'index'])->name('calculations.index');
    Route::get('calculations/{calculation}', [CalculationController::class, 'show'])->name('calculations.show');
    Route::post('calculations/run-all', [CalculationController::class, 'runAll'])->name('calculations.run-all');
    
    // Widget Management
    Route::post('widgets/reorder', [DashboardController::class, 'reorderWidgets'])->name('widgets.reorder');
    Route::post('widgets/toggle', [DashboardController::class, 'toggleWidget'])->name('widgets.toggle');
});

  ```

--------------------------------------------------------------------------------

  📄 **routes\web.php**
  ```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\OAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    // OAuth Routes
    Route::get('/auth/google', [OAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [OAuthController::class, 'handleGoogleCallback']);
    
    Route::get('/auth/github', [OAuthController::class, 'redirectToGithub'])->name('auth.github');
    Route::get('/auth/github/callback', [OAuthController::class, 'handleGithubCallback']);
    
    Route::get('/auth/facebook', [OAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/auth/facebook/callback', [OAuthController::class, 'handleFacebookCallback']);
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('admin.dashboard');
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);
    Route::post('users/{provider}/verify', [\App\Http\Controllers\Admin\UserManagementController::class, 'verifyProvider'])->name('admin.users.verify');
    
    // Dataset Management
    Route::resource('datasets', \App\Http\Controllers\Admin\DatasetController::class);
    
    // Validation Management
    Route::get('validations', [\App\Http\Controllers\Admin\ValidationController::class, 'index'])->name('admin.validations.index');
    Route::get('validations/{validation}', [\App\Http\Controllers\Admin\ValidationController::class, 'show'])->name('admin.validations.show');
    Route::post('validations/{validation}/retry', [\App\Http\Controllers\Admin\ValidationController::class, 'retry'])->name('admin.validations.retry');
});

// Statistician routes
Route::prefix('statistician')->middleware(['auth', 'role:statistician'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Statistician\DashboardController::class, 'dashboard'])->name('statistician.dashboard');
    
    // Dataset Management
    Route::resource('datasets', \App\Http\Controllers\Statistician\DatasetController::class);
    Route::post('datasets/{dataset}/verify', [\App\Http\Controllers\Statistician\DatasetController::class, 'verifyData'])->name('statistician.datasets.verify');
    
    // Calculation Rules
    Route::get('rules', [\App\Http\Controllers\Statistician\RuleController::class, 'index'])->name('statistician.rules.index');
    Route::get('rules/create', [\App\Http\Controllers\Statistician\RuleController::class, 'create'])->name('statistician.rules.create');
    Route::post('rules/test', [\App\Http\Controllers\Statistician\RuleController::class, 'testRule'])->name('statistician.rules.test');
    Route::post('rules/calculate-all', [\App\Http\Controllers\Statistician\RuleController::class, 'calculateAll'])->name('statistician.rules.calculate-all');
    
    // Calculations
    Route::get('calculations', [\App\Http\Controllers\Statistician\CalculationController::class, 'index'])->name('statistician.calculations.index');
    Route::get('calculations/{dataset}', [\App\Http\Controllers\Statistician\CalculationController::class, 'show'])->name('statistician.calculations.show');
    Route::post('calculations/run-all', [\App\Http\Controllers\Statistician\CalculationController::class, 'runAll'])->name('statistician.calculations.run-all');
});

// Provider routes
Route::prefix('provider')->middleware(['auth', 'role:provider'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Provider\DashboardController::class, 'dashboard'])->name('provider.dashboard');
    
    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\Provider\DashboardController::class, 'profile'])->name('provider.profile');
    Route::post('/profile', [\App\Http\Controllers\Provider\DashboardController::class, 'updateProfile'])->name('provider.profile.update');
    
    // Data Entry
    Route::resource('data-entry', \App\Http\Controllers\Provider\DataEntryController::class)->except(['show']);
});

// API routes for data providers
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('datasets', \App\Http\Controllers\Api\DatasetController::class)->only(['index', 'show']);
    Route::get('datasets/{dataset}/data-points', [\App\Http\Controllers\Api\DatasetController::class, 'dataPoints'])->name('api.datasets.data-points');
    
    Route::apiResource('data-points', \App\Http\Controllers\Api\DataPointController::class)->only(['store', 'update', 'destroy']);
});

// Authentication routes (Laravel Breeze)
require __DIR__.'/auth.php';

  ```

--------------------------------------------------------------------------------

📁 **tests/**
  📄 **tests\CreatesApplication.php**
  ```php
<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}

  ```

--------------------------------------------------------------------------------

  📄 **tests\TestCase.php**
  ```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Test veritabanını temizle
        if (config('database.default') === 'sqlite') {
            $databasePath = database_path('database.sqlite');
            if (!file_exists($databasePath)) {
                file_put_contents($databasePath, '');
            }
        }
    }
    
    /**
     * Helper: Admin kullanıcısı oluştur
     */
    protected function createAdminUser($attributes = [])
    {
        return \App\Models\User::factory()->create(array_merge([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ], $attributes));
    }
    
    /**
     * Helper: İstatistikçi kullanıcısı oluştur
     */
    protected function createStatisticianUser($attributes = [])
    {
        return \App\Models\User::factory()->create(array_merge([
            'role' => 'statistician',
            'email' => 'statistician@test.com',
        ], $attributes));
    }
    
    /**
     * Helper: Veri sağlayıcı kullanıcısı oluştur
     */
    protected function createProviderUser($attributes = [])
    {
        $user = \App\Models\User::factory()->create(array_merge([
            'role' => 'provider',
            'email' => 'provider@test.com',
        ], $attributes));
        
        \App\Models\DataProvider::factory()->create([
            'user_id' => $user->id,
            'organization_name' => 'Test Provider',
            'is_verified' => true,
        ]);
        
        return $user;
    }
    
    /**
     * Helper: Test veri seti oluştur
     */
    protected function createDataset($attributes = [])
    {
        return \App\Models\Dataset::factory()->create($attributes);
    }
    
    /**
     * Helper: Test veri noktası oluştur
     */
    protected function createDataPoint($attributes = [])
    {
        return \App\Models\DataPoint::factory()->create($attributes);
    }
}

  ```

--------------------------------------------------------------------------------

  📁 **Feature/**
    📄 **tests\Feature\ExampleTest.php**
    ```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

    ```

--------------------------------------------------------------------------------

    📁 **Admin/**
      📄 **tests\Feature\Admin\DashboardTest.php**
      ```php
<?php

namespace Tests\Feature\Admin;

use App\Models\Dataset;
use App\Models\User;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard()
    {
        $admin = $this->createAdminUser();
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Toplam Kullanıcı');
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $statistician = $this->createStatisticianUser();
        $provider = $this->createProviderUser();
        
        // Statistician cannot access
        $response = $this->actingAs($statistician)->get(route('admin.dashboard'));
        $response->assertStatus(403);
        
        // Provider cannot access
        $response = $this->actingAs($provider)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_dashboard_shows_correct_statistics()
    {
        $admin = $this->createAdminUser();
        
        // Create test data
        User::factory()->count(5)->create(['role' => 'provider']);
        User::factory()->count(2)->create(['role' => 'statistician']);
        
        Dataset::factory()->count(3)->create();
        
        DataPoint::factory()->count(10)->create();
        DataPoint::factory()->count(5)->create(['is_verified' => true]);
        
        DataProvider::factory()->count(4)->create();
        DataProvider::factory()->count(2)->create(['is_verified' => true]);
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('8'); // Total users (1 admin + 5 providers + 2 statisticians)
        $response->assertSee('3'); // Total datasets
        $response->assertSee('15'); // Total data points
        $response->assertSee('5'); // Verified data points
        $response->assertSee('6'); // Total providers (4 + 2 from factory)
        $response->assertSee('2'); // Verified providers
    }

    public function test_dashboard_shows_recent_users()
    {
        $admin = $this->createAdminUser();
        
        // Create recent users
        User::factory()->count(10)->create();
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Son Kayıt Olan Kullanıcılar');
        
        // Check if user data is shown
        $users = User::latest()->take(10)->get();
        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
    }

    public function test_dashboard_shows_recent_datasets()
    {
        $admin = $this->createAdminUser();
        
        // Create recent datasets
        Dataset::factory()->count(5)->create();
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Son Eklenen Veri Setleri');
        
        // Check if dataset data is shown
        $datasets = Dataset::latest()->take(5)->get();
        foreach ($datasets as $dataset) {
            $response->assertSee($dataset->name);
        }
    }
}

      ```

--------------------------------------------------------------------------------

    📁 **Auth/**
      📄 **tests\Feature\Auth\AuthenticationTest.php**
      ```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Giriş Yap');
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_authenticated_users_are_redirected_based_on_role()
    {
        // Test admin redirect
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/home');
        $response->assertRedirect(route('admin.dashboard'));
        
        // Test statistician redirect
        $statistician = User::factory()->create(['role' => 'statistician']);
        $response = $this->actingAs($statistician)->get('/home');
        $response->assertRedirect(route('statistician.dashboard'));
        
        // Test provider redirect
        $provider = User::factory()->create(['role' => 'provider']);
        $response = $this->actingAs($provider)->get('/home');
        $response->assertRedirect(route('provider.dashboard'));
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Kayıt Ol');
    }

    public function test_new_users_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('provider.profile'));
        
        // Check if user was created with provider role
        $user = User::where('email', 'test@example.com')->first();
        $this->assertEquals('provider', $user->role);
    }
}

      ```

--------------------------------------------------------------------------------

    📁 **Provider/**
      📄 **tests\Feature\Provider\DataEntryTest.php**
      ```php
<?php

namespace Tests\Feature\Provider;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_view_data_entry_index()
    {
        $provider = $this->createProviderUser();
        
        // Create some data points for this provider
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        DataPoint::factory()->count(3)->create(['data_provider_id' => $dataProvider->id]);
        
        $response = $this->actingAs($provider)->get(route('provider.data-entry.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Veri Girişlerim');
        $response->assertSee('Yeni Veri Ekle');
    }

    public function test_provider_cannot_view_data_entry_without_profile()
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'provider',
            'email' => 'noprofile@test.com',
        ]);
        
        $response = $this->actingAs($user)->get(route('provider.data-entry.index'));
        
        $response->assertRedirect(route('provider.profile'));
        $response->assertSessionHas('warning');
    }

    public function test_provider_can_create_data_point()
    {
        $provider = $this->createProviderUser();
        $dataset = Dataset::factory()->create(['is_public' => true]);
        
        $response = $this->actingAs($provider)->get(route('provider.data-entry.create'));
        $response->assertStatus(200);
        $response->assertSee('Yeni Veri Ekle');
        
        // Submit form
        $response = $this->actingAs($provider)->post(route('provider.data-entry.store'), [
            'dataset_id' => $dataset->id,
            'date' => date('Y-m-d'),
            'value' => 123.45,
            'source_url' => 'https://example.com',
            'notes' => 'Test data point',
        ]);
        
        $response->assertRedirect(route('provider.data-entry.index'));
        $response->assertSessionHas('success');
        
        // Check if data point was created
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        $this->assertDatabaseHas('data_points', [
            'dataset_id' => $dataset->id,
            'data_provider_id' => $dataProvider->id,
            'value' => 123.45,
        ]);
    }

    public function test_provider_cannot_create_duplicate_data_point()
    {
        $provider = $this->createProviderUser();
        $dataset = Dataset::factory()->create(['is_public' => true]);
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        
        // Create first data point
        DataPoint::factory()->create([
            'dataset_id' => $dataset->id,
            'data_provider_id' => $dataProvider->id,
            'date' => '2024-01-01',
        ]);
        
        // Try to create duplicate
        $response = $this->actingAs($provider)->post(route('provider.data-entry.store'), [
            'dataset_id' => $dataset->id,
            'date' => '2024-01-01',
            'value' => 999.99,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        // Should have only one data point
        $count = DataPoint::where('dataset_id', $dataset->id)
            ->where('data_provider_id', $dataProvider->id)
            ->whereDate('date', '2024-01-01')
            ->count();
        
        $this->assertEquals(1, $count);
    }

    public function test_provider_can_update_own_data_point()
    {
        $provider = $this->createProviderUser();
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        
        $dataPoint = DataPoint::factory()->create([
            'data_provider_id' => $dataProvider->id,
        ]);
        
        $response = $this->actingAs($provider)->get(route('provider.data-entry.edit', $dataPoint));
        $response->assertStatus(200);
        
        // Update data point
        $response = $this->actingAs($provider)->put(route('provider.data-entry.update', $dataPoint), [
            'value' => 999.99,
            'source_url' => 'https://updated.com',
            'notes' => 'Updated notes',
        ]);
        
        $response->assertRedirect(route('provider.data-entry.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            'value' => 999.99,
            'is_verified' => false, // Should be reset after update
        ]);
    }

    public function test_provider_cannot_update_other_providers_data_point()
    {
        $provider1 = $this->createProviderUser();
        $provider2 = $this->createProviderUser(['email' => 'provider2@test.com']);
        
        $dataProvider2 = DataProvider::where('user_id', $provider2->id)->first();
        $dataPoint = DataPoint::factory()->create([
            'data_provider_id' => $dataProvider2->id,
        ]);
        
        $response = $this->actingAs($provider1)->get(route('provider.data-entry.edit', $dataPoint));
        $response->assertStatus(403);
    }

    public function test_provider_can_delete_own_data_point()
    {
        $provider = $this->createProviderUser();
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        
        $dataPoint = DataPoint::factory()->create([
            'data_provider_id' => $dataProvider->id,
        ]);
        
        $response = $this->actingAs($provider)->delete(route('provider.data-entry.destroy', $dataPoint));
        
        $response->assertRedirect(route('provider.data-entry.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('data_points', ['id' => $dataPoint->id]);
    }

    public function test_provider_cannot_access_private_datasets()
    {
        $provider = $this->createProviderUser();
        $dataset = Dataset::factory()->create(['is_public' => false]);
        
        $response = $this->actingAs($provider)->post(route('provider.data-entry.store'), [
            'dataset_id' => $dataset->id,
            'date' => date('Y-m-d'),
            'value' => 123.45,
        ]);
        
        // Should fail because dataset is private
        $response->assertStatus(403);
    }
}

      ```

--------------------------------------------------------------------------------

    📁 **Statistician/**
      📄 **tests\Feature\Statistician\DatasetTest.php**
      ```php
<?php

namespace Tests\Feature\Statistician;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\ValidationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatasetTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistician_can_view_datasets_index()
    {
        $statistician = $this->createStatisticianUser();
        
        // Create datasets for this statistician
        Dataset::factory()->count(3)->create(['created_by' => $statistician->id]);
        
        $response = $this->actingAs($statistician)->get(route('statistician.datasets.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Veri Setlerim');
        $response->assertSee('Yeni Veri Seti');
    }

    public function test_statistician_can_create_dataset()
    {
        $statistician = $this->createStatisticianUser();
        
        $response = $this->actingAs($statistician)->get(route('statistician.datasets.create'));
        $response->assertStatus(200);
        $response->assertSee('Yeni Veri Seti Oluştur');
        
        // Submit form
        $response = $this->actingAs($statistician)->post(route('statistician.datasets.store'), [
            'name' => 'Test Dataset',
            'description' => 'Test dataset description',
            'unit' => 'TL',
            'calculation_rule' => 'ortalama(deger)',
            'is_public' => true,
        ]);
        
        $response->assertRedirect(route('statistician.datasets.index'));
        $response->assertSessionHas('success');
        
        // Check if dataset was created
        $this->assertDatabaseHas('datasets', [
            'name' => 'Test Dataset',
            'created_by' => $statistician->id,
        ]);
    }

    public function test_statistician_can_view_own_dataset()
    {
        $statistician = $this->createStatisticianUser();
        $dataset = Dataset::factory()->create(['created_by' => $statistician->id]);
        
        // Add some data points
        DataPoint::factory()->count(5)->create(['dataset_id' => $dataset->id]);
        ValidationLog::factory()->create(['dataset_id' => $dataset->id]);
        
        $response = $this->actingAs($statistician)->get(route('statistician.datasets.show', $dataset));
        
        $response->assertStatus(200);
        $response->assertSee($dataset->name);
        $response->assertSee('Veri Grafiği');
        $response->assertSee('Veri Noktaları');
    }

    public function test_statistician_cannot_view_other_statisticians_dataset()
    {
        $statistician1 = $this->createStatisticianUser();
        $statistician2 = $this->createStatisticianUser(['email' => 'statistician2@test.com']);
        
        $dataset = Dataset::factory()->create(['created_by' => $statistician1->id]);
        
        $response = $this->actingAs($statistician2)->get(route('statistician.datasets.show', $dataset));
        
        $response->assertStatus(403);
    }

    public function test_statistician_can_update_dataset()
    {
        $statistician = $this->createStatisticianUser();
        $dataset = Dataset::factory()->create(['created_by' => $statistician->id]);
        
        $response = $this->actingAs($statistician)->get(route('statistician.datasets.edit', $dataset));
        $response->assertStatus(200);
        
        // Update dataset
        $response = $this->actingAs($statistician)->put(route('statistician.datasets.update', $dataset), [
            'name' => 'Updated Dataset Name',
            'description' => 'Updated description',
            'unit' => 'USD',
            'calculation_rule' => 'topla(deger) / sayi',
            'is_public' => false,
        ]);
        
        $response->assertRedirect(route('statistician.datasets.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('datasets', [
            'id' => $dataset->id,
            'name' => 'Updated Dataset Name',
            'unit' => 'USD',
        ]);
    }

    public function test_statistician_can_delete_dataset()
    {
        $statistician = $this->createStatisticianUser();
        $dataset = Dataset::factory()->create(['created_by' => $statistician->id]);
        
        $response = $this->actingAs($statistician)->delete(route('statistician.datasets.destroy', $dataset));
        
        $response->assertRedirect(route('statistician.datasets.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('datasets', ['id' => $dataset->id]);
    }

    public function test_statistician_can_verify_data_manually()
    {
        $statistician = $this->createStatisticianUser();
        $dataset = Dataset::factory()->create(['created_by' => $statistician->id]);
        
        // Add data points for verification
        DataPoint::factory()->count(3)->create([
            'dataset_id' => $dataset->id,
            'date' => '2024-01-01',
        ]);
        
        $response = $this->actingAs($statistician)->post(route('statistician.datasets.verify', $dataset), [
            'date' => '2024-01-01',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Check if validation log was created
        $this->assertDatabaseHas('validation_logs', [
            'dataset_id' => $dataset->id,
            'date' => '2024-01-01',
        ]);
    }
}

      ```

--------------------------------------------------------------------------------

    📁 **Yeni klasör/**
  📁 **Unit/**
    📄 **tests\Unit\ExampleTest.php**
    ```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}

    ```

--------------------------------------------------------------------------------

    📁 **Services/**
      📄 **tests\Unit\Services\CalculationEngineTest.php**
      ```php
<?php

namespace Tests\Unit\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use App\Services\CalculationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected CalculationEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new CalculationEngine();
    }

    public function test_calculate_average()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'ortalama(deger)',
        ]);
        
        // Create verified data points
        DataPoint::factory()->count(5)->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 100,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(100, $result);
    }

    public function test_calculate_sum()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'topla(deger)',
        ]);
        
        DataPoint::factory()->count(3)->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 10,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(30, $result);
    }

    public function test_calculate_sum_divided_by_count()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'topla(deger) / sayi',
        ]);
        
        DataPoint::factory()->count(4)->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 40,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(40, $result); // 160 / 4 = 40
    }

    public function test_calculate_max()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'max(deger)',
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 10,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 50,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 30,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(50, $result);
    }

    public function test_calculate_min()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'min(deger)',
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 100,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 20,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 75,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(20, $result);
    }

    public function test_calculate_complex_expression()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => '(max(deger) - min(deger)) / 2',
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 10,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 30,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(10, $result); // (30 - 10) / 2 = 10
    }

    public function test_returns_null_for_invalid_rule()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'invalid_function(deger)',
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertNull($result);
    }

    public function test_returns_null_for_empty_dataset()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'ortalama(deger)',
        ]);
        
        // No data points
        $result = $this->engine->calculate($dataset);
        
        $this->assertNull($result);
    }

    public function test_returns_null_for_no_calculation_rule()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => null,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertNull($result);
    }

    public function test_only_uses_verified_data_points()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'ortalama(deger)',
        ]);
        
        // Verified data point
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 100,
        ]);
        
        // Unverified data point (should be ignored)
        DataPoint::factory()->unverified()->create([
            'dataset_id' => $dataset->id,
            'value' => 1000,
            'verified_value' => null,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(100, $result);
    }
}

      ```

--------------------------------------------------------------------------------

      📄 **tests\Unit\Services\DataVerificationServiceTest.php**
      ```php
<?php

namespace Tests\Unit\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use App\Services\DataVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataVerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DataVerificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataVerificationService();
    }

    public function test_check_and_trigger_validation_with_sufficient_data()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create 2 data points for the same date
        DataPoint::factory()->count(2)->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
        ]);
        
        $result = $this->service->checkAndTriggerValidation($dataset, $date);
        
        $this->assertTrue($result);
    }

    public function test_check_and_trigger_validation_with_insufficient_data()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create only 1 data point
        DataPoint::factory()->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
        ]);
        
        $result = $this->service->checkAndTriggerValidation($dataset, $date);
        
        $this->assertFalse($result);
    }

    public function test_process_validation_with_normal_data()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create 3 data points with similar values
        $dataPoints = DataPoint::factory()->count(3)->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
            'value' => 100, // All same value
        ]);
        
        $result = $this->service->processValidation($dataset, $date);
        
        $this->assertIsArray($result);
        $this->assertEquals(100, $result['average']);
        $this->assertEquals(0, $result['std_dev']);
        $this->assertEquals(3, $result['valid_points']);
        $this->assertEquals(3, $result['total_points']);
        $this->assertEmpty($result['outliers']);
        
        // Check if data points were verified
        foreach ($dataPoints as $dataPoint) {
            $dataPoint->refresh();
            $this->assertTrue($dataPoint->is_verified);
            $this->assertEquals(100, $dataPoint->verified_value);
        }
    }

    public function test_process_validation_with_outliers()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create data points: 2 normal, 1 outlier
        DataPoint::factory()->count(2)->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
            'value' => 100,
        ]);
        
        DataPoint::factory()->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
            'value' => 1000, // This is an outlier
        ]);
        
        $result = $this->service->processValidation($dataset, $date);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result['outliers']); // One outlier detected
        $this->assertEquals(2, $result['valid_points']); // Two valid points
        $this->assertEquals(3, $result['total_points']);
        
        // Check if outlier was marked as unverified
        $outlier = DataPoint::where('value', 1000)->first();
        $this->assertFalse($outlier->is_verified);
        $this->assertNull($outlier->verified_value);
        
        // Check if normal points were verified
        $normalPoints = DataPoint::where('value', 100)->get();
        foreach ($normalPoints as $point) {
            $this->assertTrue($point->is_verified);
            $this->assertEquals(100, $point->verified_value);
        }
    }

    public function test_calculate_average()
    {
        $values = [10, 20, 30, 40, 50];
        $average = $this->invokePrivateMethod($this->service, 'calculateAverage', [$values]);
        
        $this->assertEquals(30, $average);
    }

    public function test_calculate_standard_deviation()
    {
        $values = [10, 20, 30, 40, 50];
        $stdDev = $this->invokePrivateMethod($this->service, 'calculateStandardDeviation', [$values]);
        
        // Manual calculation: sqrt(((10-30)² + (20-30)² + (30-30)² + (40-30)² + (50-30)²) / 5)
        // = sqrt((400 + 100 + 0 + 100 + 400) / 5) = sqrt(1000 / 5) = sqrt(200) = 14.1421...
        $expectedStdDev = sqrt(200);
        
        $this->assertEqualsWithDelta($expectedStdDev, $stdDev, 0.0001);
    }

    public function test_process_validation_returns_false_for_insufficient_data()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create only 1 data point
        DataPoint::factory()->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
        ]);
        
        $result = $this->service->processValidation($dataset, $date);
        
        $this->assertFalse($result);
    }

    public function test_validation_log_is_created()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        DataPoint::factory()->count(3)->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
            'value' => 100,
        ]);
        
        $this->service->processValidation($dataset, $date);
        
        $this->assertDatabaseHas('validation_logs', [
            'dataset_id' => $dataset->id,
            'date' => $date,
            'status' => 'verified',
            'total_points' => 3,
            'valid_points' => 3,
        ]);
    }

    /**
     * Helper method to invoke private methods
     */
    private function invokePrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($object, $parameters);
    }
}

      ```

--------------------------------------------------------------------------------

