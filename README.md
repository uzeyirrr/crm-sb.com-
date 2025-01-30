# CRM Randevu Sistemi

## Kurulum Adımları

1. Projeyi klonlayın:
```bash
git clone https://github.com/uzeyirrr/crm-sb.com-.git
cd crm-sb.com
```

2. Composer bağımlılıklarını yükleyin:
```bash
composer install
```

3. `.env` dosyasını oluşturun:
```bash
cp .env.example .env
```

4. `.env` dosyasını düzenleyin:
- Veritabanı bilgilerinizi girin
- Uygulama adını ve URL'sini ayarlayın

5. Uygulama anahtarını oluşturun:
```bash
php artisan key:generate
```

6. Veritabanını oluşturun:
```bash
php artisan migrate:fresh
```

7. Admin kullanıcısını oluşturun:
```bash
php artisan orchid:admin Admin admin@admin.com password123
```

8. Geliştirme sunucusunu başlatın:
```bash
php artisan serve
```

## Erişim Bilgileri

### Admin Panel
- URL: http://localhost:8000/admin
- E-posta: admin@admin.com
- Şifre: password123

## Modüller ve Özellikler

### 1. Takvim Modülü
- Haftalık görünüm
- Kategori filtresi
- Tarih seçimi
- Zaman dilimi ekleme/düzenleme
- Randevu durumu takibi

### 2. Randevu Yönetimi
- Randevu oluşturma ve düzenleme
- Müşteri bilgileri
- Konum bilgileri
- Randevu durumu takibi
- Çatı fotoğrafı yükleme

### 3. Zaman Dilimleri
- İsim ve açıklama
- Başlangıç ve bitiş saati
- Maksimum randevu sayısı
- Kategori seçimi
- Aktiflik durumu

### 4. Kategori Yönetimi
- Kategori oluşturma ve düzenleme
- Varsayılan kategori
- Aktiflik durumu
- Alt zaman dilimlerini yönetme

### 5. Takım Yönetimi
- Takım oluşturma ve düzenleme
- Üye ekleme ve çıkarma
- Randevu atama

## Rol ve İzinler

### Admin Rolü İzinleri
1. Takvim Yönetimi
   - Takvimi görüntüleme
   - Randevuları görüntüleme/oluşturma/düzenleme
   - Zaman dilimlerini görüntüleme/oluşturma/düzenleme

2. Kategori Yönetimi
   - Kategorileri görüntüleme/oluşturma/düzenleme/silme

3. Takım Yönetimi
   - Takımları görüntüleme/oluşturma/düzenleme

4. Sistem Yönetimi
   - Kullanıcıları yönetme
   - Rolleri yönetme

## Teknik Detaylar

### Kullanılan Teknolojiler
- Laravel 10
- Orchid Admin Panel
- MySQL
- PHP 8.1+

### Veritabanı Tabloları
1. `appointments`: Randevu kayıtları
2. `time_slots`: Zaman dilimleri
3. `categories`: Kategoriler
4. `teams`: Takımlar
5. `users`: Kullanıcılar
6. `roles`: Roller

### Güvenlik Özellikleri
- Rol tabanlı yetkilendirme
- Form doğrulama
- CSRF koruması
- Soft delete desteği

## Geliştirici Notları

1. Yeni bir migration oluştururken:
```bash
php artisan make:migration migration_adi
```

2. Yeni bir model oluştururken:
```bash
php artisan make:model ModelAdi
```

3. Yeni bir controller oluştururken:
```bash
php artisan make:controller ControllerAdi
```

4. Cache temizleme:
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakın.
